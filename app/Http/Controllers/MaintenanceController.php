<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Maintenance;
use App\Models\Lamp;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    private function ensureColumnsExist(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'lamp_id')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `maintenances` ADD COLUMN `lamp_id` BIGINT UNSIGNED NULL AFTER `floor_id`");
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'work_start_time')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `maintenances` ADD COLUMN `work_start_time` VARCHAR(255) NULL");
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'work_end_time')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `maintenances` ADD COLUMN `work_end_time` VARCHAR(255) NULL");
            }
        } catch (\Throwable $e) {
            // Ignore if column already exists
        }
    }

    public function index(Request $request)
    {
        $this->ensureColumnsExist();

        $tab = $request->query('tab', 'daftar'); // daftar or jadwal
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $priority = $request->query('priority');

        $maintenancesQuery = Maintenance::with(['floor.building', 'lamp.lampType'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($priority, fn ($query) => $query->where('priority', $priority))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('assigned_to', 'like', "%{$search}%");
                });
            });

        if ($tab === 'jadwal') {
            $maintenances = $maintenancesQuery->orderBy('scheduled_date', 'asc')->get();
        } else {
            $maintenances = $maintenancesQuery->latest('scheduled_date')->latest()->get();
        }

        // Grouping for scheduler
        $today = Carbon::today();
        $startOfWeek = Carbon::today()->startOfWeek();
        $endOfWeek = Carbon::today()->endOfWeek();

        $groupedJadwal = [
            'hari_ini' => [],
            'minggu_ini' => [],
            'mendatang' => [],
            'riwayat' => [],
        ];

        foreach ($maintenancesQuery->orderBy('scheduled_date', 'asc')->get() as $item) {
            if ($item->status === 'completed' || $item->status === 'cancelled') {
                $groupedJadwal['riwayat'][] = $item;
            } else {
                $itemDate = $item->scheduled_date ? Carbon::parse($item->scheduled_date) : null;
                if (!$itemDate) {
                    $groupedJadwal['mendatang'][] = $item;
                } elseif ($itemDate->isToday()) {
                    $groupedJadwal['hari_ini'][] = $item;
                } elseif ($itemDate->between($startOfWeek, $endOfWeek)) {
                    $groupedJadwal['minggu_ini'][] = $item;
                } else {
                    $groupedJadwal['mendatang'][] = $item;
                }
            }
        }

        return view('pages.sideral.maintenance', [
            'title' => 'Maintenance',
            'tab' => $tab,
            'maintenances' => $maintenances,
            'groupedJadwal' => $groupedJadwal,
            'buildings' => Building::with(['floors' => function ($q) {
                $q->orderBy('floor_number')->orderBy('name');
            }, 'floors.lamps.lampType'])->orderBy('name')->get(),
            'lamps' => \App\Models\Lamp::with(['floor.building', 'lampType'])->orderBy('code')->get(),
            'filters' => $request->only(['status', 'priority', 'search']),
        ]);
    }

    private function syncLampStatus(Maintenance $maintenance): void
    {
        if (!$maintenance->lamp_id) return;
        $lamp = Lamp::find($maintenance->lamp_id);
        if (!$lamp) return;

        if ($maintenance->status === 'completed') {
            $lamp->update(['status' => 'on']);
        } elseif (in_array($maintenance->status, ['pending', 'in_progress'], true)) {
            $lamp->update(['status' => 'rusak']);
        }
    }

    public function store(Request $request)
    {
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'floor_id' => ['required', 'integer', 'exists:floors,id'],
            'lamp_id' => ['nullable', 'integer', 'exists:lamps,id'],
            'type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:high,medium,low'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'scheduled_date' => ['nullable', 'date'],
            'completed_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'completed' && empty($validated['completed_date'])) {
            $validated['completed_date'] = now()->toDateString();
        }

        $maintenance = Maintenance::create($validated);
        $this->syncLampStatus($maintenance);

        return redirect()->route('maintenance', ['tab' => $request->query('tab', 'daftar')])
            ->with('success', 'Maintenance ticket berhasil dibuat.');
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'floor_id' => ['required', 'integer', 'exists:floors,id'],
            'lamp_id' => ['nullable', 'integer', 'exists:lamps,id'],
            'type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:high,medium,low'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'scheduled_date' => ['nullable', 'date'],
            'completed_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'completed' && empty($validated['completed_date'])) {
            $validated['completed_date'] = now()->toDateString();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_date'] = null;
        }

        $maintenance->update($validated);
        $this->syncLampStatus($maintenance);

        return redirect()->route('maintenance', ['tab' => $request->query('tab', 'daftar')])
            ->with('success', 'Maintenance ticket berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'completed') {
            $updateData['completed_date'] = now()->toDateString();
            if ($request->filled('resolution_notes')) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }
        } else {
            $updateData['completed_date'] = null;
        }

        $maintenance->update($updateData);
        $this->syncLampStatus($maintenance);

        return redirect()->route('maintenance', ['tab' => $request->query('tab', 'jadwal')])
            ->with('success', 'Status maintenance berhasil diubah.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $tab = request()->query('tab', 'daftar');
        $maintenance->delete();

        return redirect()->route('maintenance', ['tab' => $tab])
            ->with('success', 'Maintenance ticket berhasil dihapus.');
    }

    public function work(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'completed_date' => ['required', 'date'],
            'work_start_time' => ['required', 'string'],
            'work_end_time' => ['required', 'string'],
            'assigned_to' => ['required', 'string', 'max:255'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $validated['status'] = 'in_progress';

        $maintenance->update($validated);

        return redirect()->route('maintenance', ['tab' => $request->query('tab', 'daftar')])
            ->with('success', 'Detail pengerjaan berhasil disimpan.');
    }

    public function approve(Request $request, Maintenance $maintenance)
    {
        // If a specific lamp is linked, record transaction history and set lamp to active
        if ($maintenance->lamp_id) {
            $lamp = $maintenance->lamp;
            if ($lamp) {
                // Create transaction replacement log for report tab
                Transaction::create([
                    'lamp_id'          => $lamp->id,
                    'floor_id'         => $maintenance->floor_id,
                    'lamp_type_id'     => $lamp->lamp_type_id,
                    'type'             => 'penggantian',
                    'quantity'         => 1,
                    'transaction_date' => $maintenance->completed_date ?? now()->toDateString(),
                    'technician'       => $maintenance->assigned_to ?: 'Teknisi',
                    'notes'            => $maintenance->resolution_notes ?: 'Penggantian lampu via tiket #' . $maintenance->id,
                ]);

                // Update lamp status back to normal/active
                $lamp->update(['status' => 'on']);
            }
        }

        // Set status to completed (Approved)
        $maintenance->update([
            'status'         => 'completed',
            'completed_date' => $maintenance->completed_date ?? now()->toDateString()
        ]);

        return redirect()->route('maintenance', ['tab' => $request->query('tab', 'daftar')])
            ->with('success', 'Pekerjaan berhasil disetujui (Approve).');
    }
}
