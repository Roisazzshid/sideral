<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
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
        } catch (\Exception $e) {
            // Silence any issues if schema can't be modified (e.g. database locks)
        }
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'gedung'); // gedung, lantai
        if (!in_array($tab, ['gedung', 'lantai'], true)) {
            $tab = 'gedung';
        }

        $buildings = Building::with('floors.rooms.lamps')->orderBy('name')->get();
        
        $floors = Floor::with(['building', 'rooms.lamps'])
            ->join('buildings', 'floors.building_id', '=', 'buildings.id')
            ->orderBy('buildings.name')
            ->orderBy('floors.floor_number')
            ->select('floors.*')
            ->get();

        $rooms = Room::with(['floor.building', 'lamps'])->orderBy('name')->get();

        return view('pages.sideral.master-data', [
            'title' => 'Master Data',
            'tab' => $tab,
            'buildings' => $buildings,
            'floors' => $floors,
            'rooms' => $rooms,
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

    // ── Room CRUD ──────────────────────────────────────────────────────
    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'floor_id' => ['required', 'integer', 'exists:floors,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:office,lobby,meeting_room,toilet,pantry,server_room,lounge,utility,storage,worship'],
            'description' => ['nullable', 'string'],
        ]);

        Room::create($validated);

        return redirect()->route('master-data', ['tab' => 'ruangan'])->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function updateRoom(Request $request, Room $room)
    {
        $validated = $request->validate([
            'floor_id' => ['required', 'integer', 'exists:floors,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:office,lobby,meeting_room,toilet,pantry,server_room,lounge,utility,storage,worship'],
            'description' => ['nullable', 'string'],
        ]);

        $room->update($validated);

        return redirect()->route('master-data', ['tab' => 'ruangan'])->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroyRoom(Room $room)
    {
        $room->delete();
        return redirect()->route('master-data', ['tab' => 'ruangan'])->with('success', 'Ruangan berhasil dihapus.');
    }

}
