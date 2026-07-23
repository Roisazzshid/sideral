<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Lamp;
use App\Models\LampType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FloorPlanController extends Controller
{
    /**
     * Sync floor plan images between storage/app/public and public/storage,
     * and auto-link DB floor_plan_image if file exists.
     */
    private function syncStorageFloorPlans()
    {
        try {
            $sourceDir = storage_path('app/public/floor-plans');
            $targetDir = public_path('storage/floor-plans');

            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            if (file_exists($sourceDir)) {
                $files = scandir($sourceDir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;

                    $srcFile = $sourceDir . '/' . $file;
                    $dstFile = $targetDir . '/' . $file;
                    if (!file_exists($dstFile) || filemtime($srcFile) > filemtime($dstFile)) {
                        @copy($srcFile, $dstFile);
                    }

                    $floorId = pathinfo($file, PATHINFO_FILENAME);
                    if (is_numeric($floorId)) {
                        $floor = Floor::find($floorId);
                        if ($floor && empty($floor->floor_plan_image)) {
                            $floor->update(['floor_plan_image' => $file]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Silence any filesystem permission issues
        }
    }

    /**
     * Halaman utama floor plan.
     */
    public function index()
    {
        $this->syncStorageFloorPlans();

        $buildings = Building::with(['floors' => function ($query) {
            $query->orderBy('floor_number')->orderBy('name');
        }])->get();
        $selectedBuilding = $buildings->first();
        $selectedFloor = $selectedBuilding?->floors->first();
        $lampTypes = LampType::where('status', 'aktif')->orWhereNull('status')->get();

        // Pre-serialize buildings data untuk Alpine.js (hindari arrow fn di blade @json)
        $buildingsData = $buildings->map(function ($b) {
            return [
                'id'     => $b->id,
                'name'   => $b->name,
                'floors' => $b->floors->map(function ($f) {
                    return ['id' => $f->id, 'name' => $f->name];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $selectedFloorImage = ($selectedFloor && $selectedFloor->floor_plan_image)
            ? asset('storage/floor-plans/' . basename($selectedFloor->floor_plan_image))
            : '';

        return view('pages.sideral.floor-plan', compact(
            'buildings',
            'selectedBuilding',
            'selectedFloor',
            'lampTypes',
            'buildingsData',
            'selectedFloorImage'
        ));
    }

    /**
     * AJAX: Ambil data lantai beserta lampu-lampunya.
     */
    private function ensureColumnsExist(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('lamps', 'width')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lamps` ADD COLUMN `width` INT NULL DEFAULT 32 AFTER `rotation`");
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('lamps', 'height')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lamps` ADD COLUMN `height` INT NULL DEFAULT 14 AFTER `width`");
            }
        } catch (\Throwable $e) {
            // Ignore if already exists
        }
    }

    public function getFloorData(Request $request)
    {
        try {
            $this->ensureColumnsExist();

            $floorId = $request->query('floor_id');
            $floor   = Floor::find($floorId);

            if (!$floor) {
                return response()->json(['error' => 'Floor not found'], 404);
            }

            $lamps = $floor->lamps()->with(['lampType', 'transactions', 'maintenances'])->get()->map(function ($lamp) use ($floor) {
                $historyList = [];

                if ($lamp->maintenances) {
                    foreach ($lamp->maintenances as $mt) {
                        try {
                            $d = $mt->completed_date ?: $mt->scheduled_date ?: $mt->created_at;
                            $dateStr = $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '-';
                            $ts = $d ? \Carbon\Carbon::parse($d)->timestamp : 0;
                        } catch (\Throwable $e) {
                            $dateStr = '-';
                            $ts = 0;
                        }

                        $historyList[] = [
                            'date'       => $dateStr,
                            'technician' => $mt->assigned_to ?: 'Teknisi',
                            'notes'      => ($mt->type ? '[' . $mt->type . '] ' : '') . ($mt->resolution_notes ?: $mt->description ?: 'Pemeliharaan'),
                            'ts'         => $ts,
                        ];
                    }
                }

                if ($lamp->transactions) {
                    foreach ($lamp->transactions as $tx) {
                        try {
                            $d = $tx->transaction_date ?: $tx->created_at;
                            $dateStr = $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '-';
                            $ts = $d ? \Carbon\Carbon::parse($d)->timestamp : 0;
                        } catch (\Throwable $e) {
                            $dateStr = '-';
                            $ts = 0;
                        }

                        $historyList[] = [
                            'date'       => $dateStr,
                            'technician' => $tx->technician ?: 'Teknisi',
                            'notes'      => $tx->notes ?: 'Penggantian Lampu',
                            'ts'         => $ts,
                        ];
                    }
                }

                usort($historyList, fn($a, $b) => $b['ts'] <=> $a['ts']);

                return [
                    'id'         => $lamp->id,
                    'code'       => $lamp->code,
                    'position_x' => $lamp->position_x,
                    'position_y' => $lamp->position_y,
                    'rotation'   => $lamp->rotation ?? 0,
                    'width'      => $lamp->width ?? 32,
                    'height'     => $lamp->height ?? 14,
                    'status'     => $lamp->status ?? 'off',
                    'floor'      => [
                        'id'   => $floor->id,
                        'name' => $floor->name,
                    ],
                    'lamp_type'  => $lamp->lampType ? [
                        'id'    => $lamp->lampType->id,
                        'name'  => $lamp->lampType->name,
                        'type'  => $lamp->lampType->type,
                        'shape' => $lamp->lampType->shape ?? 'bulat',
                        'watt'  => $lamp->lampType->watt,
                    ] : null,
                    'history'    => array_slice($historyList, 0, 10),
                ];
            });

            // Hitung semua lampu di lantai ini
            $lampCounts = [
                'on'      => $lamps->where('status', 'on')->count(),
                'off'     => $lamps->where('status', 'off')->count(),
                'rusak'   => $lamps->where('status', 'rusak')->count(),
            ];

            $floorImage = '';
            if ($floor->floor_plan_image) {
                $floorImage = asset('storage/floor-plans/' . basename($floor->floor_plan_image));
            }

            return response()->json([
                'floor'       => [
                    'id'               => $floor->id,
                    'name'             => $floor->name,
                    'floor_number'     => $floor->floor_number,
                    'floor_plan_image' => $floorImage,
                ],
                'lamps'       => $lamps,
                'lamp_counts' => $lampCounts,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error getFloorData: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Server Error: ' . $e->getMessage(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: Simpan titik lampu baru pada canvas.
     */
    public function saveLamp(Request $request)
    {
        $validated = $request->validate([
            'floor_id'      => 'required|integer|exists:floors,id',
            'lamp_type_id'  => 'required|integer|exists:lamp_types,id',
            'position_x'    => 'required|numeric|min:0|max:100',
            'position_y'    => 'required|numeric|min:0|max:100',
        ]);

        // Generate kode unik
        $nextId = (Lamp::max('id') ?? 0) + 1;
        $code = 'L-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $lamp = Lamp::create([
            'floor_id'     => $validated['floor_id'],
            'lamp_type_id' => $validated['lamp_type_id'],
            'code'         => $code,
            'position_x'   => $validated['position_x'],
            'position_y'   => $validated['position_y'],
            'status'       => 'on',
        ]);

        $lamp->load(['lampType', 'floor']);

        return response()->json([
            'id'         => $lamp->id,
            'code'       => $lamp->code,
            'position_x' => $lamp->position_x,
            'position_y' => $lamp->position_y,
            'rotation'   => $lamp->rotation ?? 0,
            'width'      => $lamp->width ?? 32,
            'height'     => $lamp->height ?? 14,
            'status'     => $lamp->status,
            'floor'      => [
                'id'   => $lamp->floor->id,
                'name' => $lamp->floor->name,
            ],
            'lamp_type'  => $lamp->lampType ? [
                'id'    => $lamp->lampType->id,
                'name'  => $lamp->lampType->name,
                'type'  => $lamp->lampType->type,
                'shape' => $lamp->lampType->shape ?? 'bulat',
                'watt'  => $lamp->lampType->watt,
            ] : null,
            'history'    => [],
        ], 201);
    }

    /**
     * AJAX: Update posisi lampu.
     */
    public function updateLampPosition(Request $request, $lampId)
    {
        $validated = $request->validate([
            'position_x' => 'required|numeric|min:0|max:100',
            'position_y' => 'required|numeric|min:0|max:100',
        ]);

        $lamp = Lamp::findOrFail($lampId);
        $lamp->update($validated);

        return response()->json(['success' => true, 'lamp' => $lamp]);
    }

    /**
     * AJAX: Update status lampu.
     */
    public function updateLampStatus(Request $request, $lampId)
    {
        $validated = $request->validate([
            'status' => 'required|in:on,off,rusak',
        ]);

        $lamp = Lamp::findOrFail($lampId);
        $lamp->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'status' => $lamp->status]);
    }

    /**
     * AJAX: Hapus titik lampu.
     */
    public function deleteLamp($lampId)
    {
        $lamp = Lamp::findOrFail($lampId);
        $lamp->delete();

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Update rotasi lampu (terutama untuk lampu TL).
     */
    public function updateLampRotation(Request $request, $lampId)
    {
        $validated = $request->validate([
            'rotation' => 'required|integer|min:0|max:359',
        ]);

        $lamp = Lamp::findOrFail($lampId);
        $lamp->update(['rotation' => $validated['rotation']]);

        return response()->json(['success' => true, 'rotation' => $lamp->rotation]);
    }

    /**
     * AJAX: Update dimensi panjang & lebar lampu.
     */
    public function updateLampDimensions(Request $request, $lampId)
    {
        $validated = $request->validate([
            'width'  => 'required|integer|min:10|max:300',
            'height' => 'required|integer|min:4|max:150',
        ]);

        $lamp = Lamp::findOrFail($lampId);
        $lamp->update([
            'width'  => $validated['width'],
            'height' => $validated['height'],
        ]);

        return response()->json([
            'success' => true,
            'width'   => $lamp->width,
            'height'  => $lamp->height,
        ]);
    }

    /**
     * Upload gambar denah lantai.
     */
    public function uploadFloorPlan(Request $request, $floorId)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $floor = Floor::findOrFail($floorId);

        // Hapus gambar lama jika ada
        if ($floor->floor_plan_image) {
            Storage::disk('public')->delete('floor-plans/' . basename($floor->floor_plan_image));
            @unlink(public_path('storage/floor-plans/' . basename($floor->floor_plan_image)));
        }

        $ext      = $request->file('image')->getClientOriginalExtension();
        $filename = $floorId . '.' . $ext;
        $request->file('image')->storeAs('floor-plans', $filename, 'public');

        // Copy langsung ke public/storage/floor-plans jika public/storage bukan symlink
        $targetDir = public_path('storage/floor-plans');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }
        @copy(storage_path('app/public/floor-plans/' . $filename), $targetDir . '/' . $filename);

        $floor->update(['floor_plan_image' => $filename]);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/floor-plans/' . $filename),
        ]);
    }
}
