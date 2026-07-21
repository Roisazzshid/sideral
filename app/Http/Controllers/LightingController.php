<?php

namespace App\Http\Controllers;

use App\Models\LampType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LightingController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $lampTypes = LampType::query()
            ->withCount('lamps')
            ->with('inventory')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('watt', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $totalTypes = $lampTypes->count();
        $totalStock = $lampTypes->sum(fn ($lampType) => $lampType->inventory?->stock_quantity ?? 0);
        $activeTypes = $lampTypes->where('status', 'aktif')->count();
        $lowStockTypes = $lampTypes->filter(function ($lampType) {
            return in_array($this->stockStatus($lampType)['key'], ['menipis', 'warning', 'habis'], true);
        })->count();

        return view('pages.sideral.lighting', [
            'title' => 'Lighting',
            'lampTypes' => $lampTypes,
            'search' => $search,
            'totalTypes' => $totalTypes,
            'totalStock' => $totalStock,
            'activeTypes' => $activeTypes,
            'lowStockTypes' => $lowStockTypes,
            'stockStatusResolver' => fn (LampType $lampType) => $this->stockStatus($lampType),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated) {
            $lampType = LampType::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'watt' => $validated['watt'],
                'price' => $validated['price'] ?? 0,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            $lampType->inventory()->create([
                'stock_quantity' => $validated['stock_quantity'],
                'min_stock' => $validated['min_stock'],
            ]);
        });

        return redirect()->route('inventory', ['tab' => 'data-lampu'])->with('success', 'Data lampu berhasil ditambahkan.');
    }

    public function update(Request $request, LampType $lampType)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($lampType, $validated) {
            $lampType->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'watt' => $validated['watt'],
                'price' => $validated['price'] ?? 0,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            $lampType->inventory()->updateOrCreate(
                ['lamp_type_id' => $lampType->id],
                [
                    'stock_quantity' => $validated['stock_quantity'],
                    'min_stock' => $validated['min_stock'],
                ]
            );
        });

        return redirect()->route('inventory', ['tab' => 'data-lampu'])->with('success', 'Data lampu berhasil diperbarui.');
    }

    public function destroy(LampType $lampType)
    {
        $lampType->delete();

        return redirect()->route('inventory', ['tab' => 'data-lampu'])->with('success', 'Data lampu berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'watt' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function stockStatus(LampType $lampType): array
    {
        $stock = $lampType->inventory?->stock_quantity ?? 0;
        $minStock = max(1, $lampType->inventory?->min_stock ?? 10);

        if ($stock <= 0) {
            return [
                'key' => 'habis',
                'label' => 'Habis',
                'class' => 'bg-red-50 text-red-700 ring-red-200',
            ];
        }

        if ($stock <= $minStock) {
            return [
                'key' => 'warning',
                'label' => 'Warning',
                'class' => 'bg-amber-50 text-amber-700 ring-amber-200',
            ];
        }

        if ($stock <= ($minStock * 6)) {
            return [
                'key' => 'menipis',
                'label' => 'Menipis',
                'class' => 'bg-orange-50 text-orange-700 ring-orange-200',
            ];
        }

        return [
            'key' => 'aman',
            'label' => 'Aman',
            'class' => 'bg-green-50 text-green-700 ring-green-200',
        ];
    }
}
