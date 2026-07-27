<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class MasterDataController extends Controller
{
    public function __construct()
    {
        // Self-healing schema update to add 'role' column to 'users' table if it does not exist
        try {
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'role')) {
                Schema::table('users', function ($table) {
                    $table->string('role')->default('operator');
                });
            }
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'plain_password')) {
                Schema::table('users', function ($table) {
                    $table->string('plain_password')->nullable();
                });
            }
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'building_id')) {
                Schema::table('users', function ($table) {
                    $table->unsignedBigInteger('building_id')->nullable()->after('role');
                    $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
                });
            }
        } catch (\Exception $e) {
            // Silence any issues if schema can't be modified (e.g. database locks)
        }
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'gedung'); // gedung, lantai, teknisi
        if (!in_array($tab, ['gedung', 'lantai', 'teknisi'], true)) {
            $tab = 'gedung';
        }

        $buildings = Building::with('floors.lamps')->orderBy('name')->get();
        
        $floors = Floor::with(['building', 'lamps'])
            ->join('buildings', 'floors.building_id', '=', 'buildings.id')
            ->orderBy('buildings.name')
            ->orderBy('floors.floor_number')
            ->select('floors.*')
            ->get();

        $technicians = User::with('building')->where('role', 'teknisi')->orderBy('name')->get();

        return view('pages.sideral.master-data', [
            'title' => 'Master Data',
            'tab' => $tab,
            'buildings' => $buildings,
            'floors' => $floors,
            'technicians' => $technicians,
        ]);
    }

    // ── Building CRUD ──────────────────────────────────────────────────
    public function storeBuilding(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Building::create($validated);

        return redirect()->route('master-data', ['tab' => 'gedung'])->with('success', 'Gedung berhasil ditambahkan.');
    }

    public function updateBuilding(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $building->update($validated);

        return redirect()->route('master-data', ['tab' => 'gedung'])->with('success', 'Gedung berhasil diperbarui.');
    }

    public function destroyBuilding(Building $building)
    {
        $building->delete();
        return redirect()->route('master-data', ['tab' => 'gedung'])->with('success', 'Gedung berhasil dihapus.');
    }

    // ── Floor CRUD ─────────────────────────────────────────────────────
    public function storeFloor(Request $request)
    {
        $validated = $request->validate([
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'name' => ['required', 'string', 'max:255'],
            'floor_number' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
        ]);

        Floor::create($validated);

        return redirect()->route('master-data', ['tab' => 'lantai'])->with('success', 'Lantai berhasil ditambahkan.');
    }

    public function updateFloor(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'name' => ['required', 'string', 'max:255'],
            'floor_number' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
        ]);

        $floor->update($validated);

        return redirect()->route('master-data', ['tab' => 'lantai'])->with('success', 'Lantai berhasil diperbarui.');
    }

    public function destroyFloor(Floor $floor)
    {
        $floor->delete();
        return redirect()->route('master-data', ['tab' => 'lantai'])->with('success', 'Lantai berhasil dihapus.');
    }

    // ── Technician CRUD ───────────────────────────────────────────────
    public function storeTechnician(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
            'building_id' => $validated['building_id'] ?? null,
            'role' => 'teknisi',
        ]);

        return redirect()->route('master-data', ['tab' => 'teknisi'])->with('success', 'Teknisi berhasil ditambahkan.');
    }

    public function updateTechnician(Request $request, User $technician)
    {
        if ($technician->role !== 'teknisi') {
            abort(403, 'Hanya dapat memperbarui teknisi.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($technician->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'building_id' => $validated['building_id'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
            $updateData['plain_password'] = $validated['password'];
        }

        $technician->update($updateData);

        return redirect()->route('master-data', ['tab' => 'teknisi'])->with('success', 'Teknisi berhasil diperbarui.');
    }

    public function destroyTechnician(User $technician)
    {
        if ($technician->role !== 'teknisi') {
            abort(403, 'Hanya dapat menghapus teknisi.');
        }

        $technician->delete();
        return redirect()->route('master-data', ['tab' => 'teknisi'])->with('success', 'Teknisi berhasil dihapus.');
    }
}
