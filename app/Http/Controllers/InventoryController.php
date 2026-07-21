<?php

namespace App\Http\Controllers;

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
        $countPanjang = $lampTypes->where('shape', 'panjang')->count();
        $countAktif = $lampTypes->where('status', 'aktif')->count();

        return view('pages.sideral.inventory', [
            'title' => 'Jenis Lampu',
            'lampTypes' => $lampTypes,
            'search' => $search,
            'shapeFilter' => $shapeFilter,
            'statusFilter' => $statusFilter,
            'totalTypes' => $totalTypes,
            'countBulat' => $countBulat,
            'countPanjang' => $countPanjang,
            'countAktif' => $countAktif,
        ]);
    }

    public function storeLampType(Request $request)
    {
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'shape' => ['required', 'in:bulat,panjang'],
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
            'shape' => ['required', 'in:bulat,panjang'],
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
}
