<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Lamp;
use App\Models\LampType;
use App\Models\Room;
use App\Models\Transaction as LampTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'penggantian');
        $search = trim((string) $request->query('search', ''));

        $transactions = LampTransaction::with(['room.floor.building', 'lampType', 'lamp'])
            ->when(in_array($tab, ['penggantian', 'pemasangan'], true), fn ($query) => $query->where('type', $tab))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('transaction_date', '<=', $request->date_to))
            ->when($request->filled('building_id'), function ($query) use ($request) {
                $query->whereHas('room.floor', fn ($query) => $query->where('building_id', $request->building_id));
            })
            ->when($request->filled('floor_id'), function ($query) use ($request) {
                $query->whereHas('room', fn ($query) => $query->where('floor_id', $request->floor_id));
            })
            ->when($request->filled('room_id'), fn ($query) => $query->where('room_id', $request->room_id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('technician', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('room', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('lampType', fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('type', 'like', "%{$search}%"));
                });
            })
            ->latest('transaction_date')
            ->latest()
            ->get();

        return view('pages.sideral.transactions', [
            'title' => 'Transaksi',
            'tab' => $tab,
            'transactions' => $transactions,
            'buildings' => Building::with('floors.rooms')->orderBy('name')->get(),
            'rooms' => Room::with('floor.building')->orderBy('name')->get(),
            'lampTypes' => LampType::with('inventory')->where('status', 'aktif')->orderBy('name')->get(),
            'lamps' => Lamp::with('room.floor.building', 'lampType')->orderBy('code')->get(),
            'filters' => $request->only(['date_from', 'date_to', 'building_id', 'floor_id', 'room_id', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated) {
            $this->consumeStock((int) $validated['lamp_type_id'], (int) $validated['quantity'], 'Transaksi lampu baru');

            $transaction = LampTransaction::create($validated);

            if ($transaction->lamp_id && $transaction->type === 'penggantian') {
                $transaction->lamp()->update(['status' => 'on']);
            }
        });

        return redirect()->route('transactions', ['tab' => $validated['type']])->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, LampTransaction $transaction)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($transaction, $validated) {
            $this->returnStock($transaction->lamp_type_id, $transaction->quantity, 'Koreksi transaksi lama');
            $this->consumeStock((int) $validated['lamp_type_id'], (int) $validated['quantity'], 'Koreksi transaksi baru');

            $transaction->update($validated);

            if ($transaction->lamp_id && $transaction->type === 'penggantian') {
                $transaction->lamp()->update(['status' => 'on']);
            }
        });

        return redirect()->route('transactions', ['tab' => $validated['type']])->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(LampTransaction $transaction)
    {
        $tab = $transaction->type;

        DB::transaction(function () use ($transaction) {
            $this->returnStock($transaction->lamp_type_id, $transaction->quantity, 'Pembatalan transaksi lampu');
            $transaction->delete();
        });

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus dan stok dikembalikan.');
    }

    private function rules(): array
    {
        return [
            'lamp_id' => ['nullable', 'integer', 'exists:lamps,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'lamp_type_id' => ['required', 'integer', 'exists:lamp_types,id'],
            'type' => ['required', 'in:penggantian,pemasangan'],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'technician' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function consumeStock(int $lampTypeId, int $quantity, string $notes): void
    {
        $inventory = Inventory::where('lamp_type_id', $lampTypeId)->lockForUpdate()->first();

        if (!$inventory) {
            throw ValidationException::withMessages(['lamp_type_id' => 'Inventory untuk jenis lampu ini belum tersedia.']);
        }

        if ($quantity > $inventory->stock_quantity) {
            throw ValidationException::withMessages(['quantity' => 'Jumlah transaksi melebihi stok tersedia.']);
        }

        $inventory->update(['stock_quantity' => $inventory->stock_quantity - $quantity]);

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'type' => 'keluar',
            'quantity' => $quantity,
            'transaction_date' => now()->toDateString(),
            'reference' => 'TRX-LAMP',
            'notes' => $notes,
        ]);
    }

    private function returnStock(int $lampTypeId, int $quantity, string $notes): void
    {
        $inventory = Inventory::where('lamp_type_id', $lampTypeId)->lockForUpdate()->first();

        if (!$inventory) {
            return;
        }

        $inventory->update(['stock_quantity' => $inventory->stock_quantity + $quantity]);

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'type' => 'masuk',
            'quantity' => $quantity,
            'transaction_date' => now()->toDateString(),
            'reference' => 'TRX-LAMP',
            'notes' => $notes,
        ]);
    }
}
