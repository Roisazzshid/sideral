<?php

namespace App\Http\Controllers;

use App\Models\Lamp;
use App\Models\LampType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    private function ensureColumnsExist(): void
    {
        try {
            if (!Schema::hasColumn('lamp_types', 'shape')) {
                DB::statement("ALTER TABLE `lamp_types` ADD COLUMN `shape` VARCHAR(255) NOT NULL DEFAULT 'bulat' AFTER `type`");
            }
        } catch (\Throwable $e) {
            // Ignore if already exists
        }
    }

    public function index(Request $request)
    {
        $this->ensureColumnsExist();

        $search = trim((string) $request->query('search', ''));
        $shapeFilter = $request->query('shape');
        $statusFilter = $request->query('status');

        $lampTypes = LampType::query()
            ->withCount('lamps')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%")
                      ->orWhere('watt', 'like', "%{$search}%");
                });
            })
            ->when($shapeFilter, fn ($q) => $q->where('shape', $shapeFilter))
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->orderBy('name')
            ->get();

        $totalTypes = $lampTypes->count();
        $countBulat = $lampTypes->where('shape', 'bulat')->count();
        $countSegitiga = $lampTypes->where('shape', 'segitiga')->count();
        $countGaris = $lampTypes->where('shape', 'garis')->count();
        $countPersegiPanjang = $lampTypes->where('shape', 'persegi_panjang')->count();
        $countAktif = $lampTypes->where('status', 'aktif')->count();

        $tab = $request->query('tab', 'jenis-lampu');

        $lamps = Lamp::with(['floor.building', 'lampType'])
            ->orderBy('code')
            ->get();

        return view('pages.sideral.inventory', [
            'title' => 'Inventory',
            'tab' => $tab,
            'lamps' => $lamps,
            'lampTypes' => $lampTypes,
            'search' => $search,
            'shapeFilter' => $shapeFilter,
            'statusFilter' => $statusFilter,
            'totalTypes' => $totalTypes,
            'countBulat' => $countBulat,
            'countSegitiga' => $countSegitiga,
            'countGaris' => $countGaris,
            'countPersegiPanjang' => $countPersegiPanjang,
            'countAktif' => $countAktif,
        ]);
    }

    public function storeLampType(Request $request)
    {
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'shape' => ['required', 'in:bulat,segitiga,garis,persegi_panjang'],
            'watt' => ['nullable', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        if (empty($validated['watt'])) {
            $validated['watt'] = 0;
        }
        if (empty($validated['price'])) {
            $validated['price'] = 0;
        }

        LampType::create($validated);

        return redirect()->route('inventory')->with('success', 'Jenis lampu berhasil ditambahkan.');
    }

    public function updateLampType(Request $request, LampType $lampType)
    {
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'shape' => ['required', 'in:bulat,segitiga,garis,persegi_panjang'],
            'watt' => ['nullable', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        if (empty($validated['watt'])) {
            $validated['watt'] = 0;
        }
        if (empty($validated['price'])) {
            $validated['price'] = 0;
        }

        $lampType->update($validated);

        return redirect()->route('inventory')->with('success', 'Jenis lampu berhasil diperbarui.');
    }

    public function destroyLampType(LampType $lampType)
    {
        if ($lampType->lamps()->count() > 0) {
            return redirect()->route('inventory')
                ->with('error', 'Jenis lampu tidak bisa dihapus karena sedang terpasang pada titik lampu.');
        }

        $lampType->delete();

        return redirect()->route('inventory')->with('success', 'Jenis lampu berhasil dihapus.');
    }

    public function updateLamp(Request $request, Lamp $lamp)
    {
        $validated = $request->validate([
            'code'           => ['required', 'string', 'max:50'],
            'status'         => ['required', 'in:on,off,rusak,perbaikan'],
            'installed_date' => ['nullable', 'date'],
            'notes'          => ['nullable', 'string'],
        ]);

        $lamp->update($validated);

        return redirect()->route('inventory', ['tab' => 'lampu-terpasang'])->with('success', 'Data titik lampu berhasil diperbarui.');
    }

    public function destroyLamp(Lamp $lamp)
    {
        $lamp->delete();

        return redirect()->route('inventory', ['tab' => 'lampu-terpasang'])->with('success', 'Titik lampu berhasil dihapus.');
    }

    public function lampHistory(Lamp $lamp)
    {
        $maintenances = $lamp->maintenances()
            ->with('technician')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($m) {
                return [
                    'id'             => $m->id,
                    'type'           => $m->type,
                    'status'         => $m->status,
                    'priority'       => $m->priority,
                    'scheduled_date' => $m->scheduled_date?->format('d/m/Y'),
                    'completed_date' => $m->completed_date?->format('d/m/Y'),
                    'technician'     => $m->technician?->name ?? '-',
                    'notes'          => $m->resolution_notes,
                ];
            });

        $transactions = $lamp->transactions()
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'date'  => $t->created_at->format('d/m/Y'),
                    'type'  => $t->type ?? '-',
                    'notes' => $t->notes ?? '-',
                ];
            });

        return response()->json([
            'lamp'         => [
                'code'   => $lamp->code,
                'status' => $lamp->status,
            ],
            'maintenances' => $maintenances,
            'transactions' => $transactions,
        ]);
    }
}
