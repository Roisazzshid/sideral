<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\LampType;
use App\Models\Lamp;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Transaction;
use App\Models\Maintenance;
use Carbon\Carbon;

class FmLightningSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. GEDUNG (1 gedung)
        // ============================================
        $building = Building::create([
            'name' => 'Gedung A',
            'location' => 'Jakarta Pusat',
            'description' => 'Gedung utama kantor pusat',
        ]);

        // ============================================
        // 2. LANTAI (4 lantai: 1, 2, 3, PH)
        // ============================================
        $floors = [];
        $floorData = [
            ['name' => 'Lantai 1', 'floor_number' => 1],
            ['name' => 'Lantai 2', 'floor_number' => 2],
            ['name' => 'Lantai 3', 'floor_number' => 3],
            ['name' => 'Lantai PH', 'floor_number' => 4],
        ];

        foreach ($floorData as $fd) {
            $floors[] = Floor::create([
                'building_id' => $building->id,
                'name' => $fd['name'],
                'floor_number' => $fd['floor_number'],
            ]);
        }

        // ============================================
        // 3. RUANGAN per lantai
        // ============================================
        $rooms = [];

        // Lantai 1
        $roomsData = [
            // Lantai 1
            [
                'floor_index' => 0,
                'rooms' => [
                    ['name' => 'Lobby', 'type' => 'lobby'],
                    ['name' => 'Resepsionis', 'type' => 'office'],
                    ['name' => 'Meeting Room 1', 'type' => 'meeting_room'],
                    ['name' => 'Toilet Lt.1', 'type' => 'toilet'],
                    ['name' => 'Pantry & Lounge', 'type' => 'pantry'],
                    ['name' => 'Security Post', 'type' => 'office'],
                ],
            ],
            // Lantai 2
            [
                'floor_index' => 1,
                'rooms' => [
                    ['name' => 'Open Workspace A', 'type' => 'office'],
                    ['name' => 'Open Workspace B', 'type' => 'office'],
                    ['name' => 'Meeting Room 2', 'type' => 'meeting_room'],
                    ['name' => 'Meeting Room 3', 'type' => 'meeting_room'],
                    ['name' => 'Toilet Lt.2', 'type' => 'toilet'],
                    ['name' => 'Server Room', 'type' => 'server_room'],
                ],
            ],
            // Lantai 3
            [
                'floor_index' => 2,
                'rooms' => [
                    ['name' => 'Ruang Direktur', 'type' => 'office'],
                    ['name' => 'Ruang Manager', 'type' => 'office'],
                    ['name' => 'Meeting Room 4', 'type' => 'meeting_room'],
                    ['name' => 'Open Workspace C', 'type' => 'office'],
                    ['name' => 'Toilet Lt.3', 'type' => 'toilet'],
                    ['name' => 'Ruang Arsip', 'type' => 'storage'],
                ],
            ],
            // Lantai PH
            [
                'floor_index' => 3,
                'rooms' => [
                    ['name' => 'Rooftop Lounge', 'type' => 'lounge'],
                    ['name' => 'Ruang Mesin', 'type' => 'utility'],
                    ['name' => 'Musholla', 'type' => 'worship'],
                    ['name' => 'Pantry PH', 'type' => 'pantry'],
                ],
            ],
        ];

        foreach ($roomsData as $rd) {
            foreach ($rd['rooms'] as $roomInfo) {
                $rooms[] = Room::create([
                    'floor_id' => $floors[$rd['floor_index']]->id,
                    'name' => $roomInfo['name'],
                    'type' => $roomInfo['type'],
                ]);
            }
        }

        // ============================================
        // 4. JENIS LAMPU
        // ============================================
        $lampTypes = [
            LampType::create(['name' => 'Philips LED Tube 18W', 'type' => 'LED Tube', 'watt' => 18, 'price' => 45000, 'status' => 'aktif']),
            LampType::create(['name' => 'Philips LED Tube 12W', 'type' => 'LED Tube', 'watt' => 12, 'price' => 35000, 'status' => 'aktif']),
            LampType::create(['name' => 'Philips LED Bulb 12W', 'type' => 'LED Bulb', 'watt' => 12, 'price' => 28000, 'status' => 'aktif']),
            LampType::create(['name' => 'Osram LED Downlight 15W', 'type' => 'Downlight', 'watt' => 15, 'price' => 65000, 'status' => 'aktif']),
            LampType::create(['name' => 'Panasonic LED Panel 24W', 'type' => 'Panel', 'watt' => 24, 'price' => 120000, 'status' => 'aktif']),
            LampType::create(['name' => 'Philips LED Spotlight 7W', 'type' => 'Spotlight', 'watt' => 7, 'price' => 55000, 'status' => 'aktif']),
        ];

        // ============================================
        // 5. TITIK LAMPU (distribusi per ruangan)
        // ============================================
        $lampCounter = 1;
        $lampDistribution = [
            // Lantai 1 rooms (index 0-5)
            0 => [['type_idx' => 3, 'count' => 12], ['type_idx' => 5, 'count' => 6]], // Lobby: downlight + spotlight
            1 => [['type_idx' => 0, 'count' => 8]], // Resepsionis: LED Tube 18W
            2 => [['type_idx' => 4, 'count' => 6]], // Meeting Room 1: Panel
            3 => [['type_idx' => 3, 'count' => 4]], // Toilet: Downlight
            4 => [['type_idx' => 2, 'count' => 6], ['type_idx' => 5, 'count' => 4]], // Pantry: Bulb + Spotlight
            5 => [['type_idx' => 0, 'count' => 4]], // Security: LED Tube
            // Lantai 2 rooms (index 6-11)
            6 => [['type_idx' => 4, 'count' => 20], ['type_idx' => 3, 'count' => 8]], // Open Workspace A
            7 => [['type_idx' => 4, 'count' => 18], ['type_idx' => 3, 'count' => 6]], // Open Workspace B
            8 => [['type_idx' => 4, 'count' => 6]], // Meeting Room 2
            9 => [['type_idx' => 4, 'count' => 6]], // Meeting Room 3
            10 => [['type_idx' => 3, 'count' => 4]], // Toilet Lt.2
            11 => [['type_idx' => 0, 'count' => 6]], // Server Room
            // Lantai 3 rooms (index 12-17)
            12 => [['type_idx' => 3, 'count' => 8], ['type_idx' => 5, 'count' => 4]], // Ruang Direktur
            13 => [['type_idx' => 3, 'count' => 6], ['type_idx' => 5, 'count' => 2]], // Ruang Manager
            14 => [['type_idx' => 4, 'count' => 6]], // Meeting Room 4
            15 => [['type_idx' => 4, 'count' => 16], ['type_idx' => 3, 'count' => 6]], // Open Workspace C
            16 => [['type_idx' => 3, 'count' => 4]], // Toilet Lt.3
            17 => [['type_idx' => 1, 'count' => 4]], // Ruang Arsip
            // Lantai PH rooms (index 18-21)
            18 => [['type_idx' => 5, 'count' => 10], ['type_idx' => 2, 'count' => 6]], // Rooftop Lounge
            19 => [['type_idx' => 0, 'count' => 4]], // Ruang Mesin
            20 => [['type_idx' => 3, 'count' => 4]], // Musholla
            21 => [['type_idx' => 2, 'count' => 4]], // Pantry PH
        ];

        $statuses = ['on', 'on', 'on', 'on', 'on', 'on', 'on', 'on', 'off', 'rusak', 'warning'];

        foreach ($lampDistribution as $roomIdx => $types) {
            foreach ($types as $typeInfo) {
                for ($i = 0; $i < $typeInfo['count']; $i++) {
                    $status = $statuses[array_rand($statuses)];
                    Lamp::create([
                        'room_id' => $rooms[$roomIdx]->id,
                        'lamp_type_id' => $lampTypes[$typeInfo['type_idx']]->id,
                        'code' => 'L-' . str_pad($lampCounter++, 4, '0', STR_PAD_LEFT),
                        'position_x' => rand(10, 90),
                        'position_y' => rand(10, 90),
                        'status' => $status,
                        'installed_date' => Carbon::now()->subDays(rand(30, 365)),
                    ]);
                }
            }
        }

        // ============================================
        // 6. INVENTORY (stok per jenis lampu)
        // ============================================
        $inventoryData = [
            ['lamp_type_idx' => 0, 'stock' => 120, 'min_stock' => 20],
            ['lamp_type_idx' => 1, 'stock' => 85, 'min_stock' => 15],
            ['lamp_type_idx' => 2, 'stock' => 60, 'min_stock' => 15],
            ['lamp_type_idx' => 3, 'stock' => 5, 'min_stock' => 10],  // menipis
            ['lamp_type_idx' => 4, 'stock' => 45, 'min_stock' => 10],
            ['lamp_type_idx' => 5, 'stock' => 0, 'min_stock' => 10],  // habis
        ];

        $inventories = [];
        foreach ($inventoryData as $inv) {
            $inventories[] = Inventory::create([
                'lamp_type_id' => $lampTypes[$inv['lamp_type_idx']]->id,
                'stock_quantity' => $inv['stock'],
                'min_stock' => $inv['min_stock'],
            ]);
        }

        // ============================================
        // 7. INVENTORY TRANSACTIONS
        // ============================================
        foreach ($inventories as $inv) {
            // Beberapa transaksi masuk
            for ($i = 0; $i < rand(2, 5); $i++) {
                InventoryTransaction::create([
                    'inventory_id' => $inv->id,
                    'type' => 'masuk',
                    'quantity' => rand(10, 50),
                    'transaction_date' => Carbon::now()->subDays(rand(1, 180)),
                    'reference' => 'PO-' . rand(1000, 9999),
                    'notes' => 'Pembelian stok lampu',
                ]);
            }
            // Beberapa transaksi keluar
            for ($i = 0; $i < rand(1, 3); $i++) {
                InventoryTransaction::create([
                    'inventory_id' => $inv->id,
                    'type' => 'keluar',
                    'quantity' => rand(2, 15),
                    'transaction_date' => Carbon::now()->subDays(rand(1, 90)),
                    'notes' => 'Penggantian lampu rusak',
                ]);
            }
        }

        // ============================================
        // 8. TRANSAKSI (penggantian & pemasangan lampu)
        // ============================================
        $transactionData = [
            ['room_idx' => 2, 'lamp_type_idx' => 4, 'type' => 'penggantian', 'qty' => 2, 'days_ago' => 5, 'tech' => 'Ahmad'],
            ['room_idx' => 6, 'lamp_type_idx' => 4, 'type' => 'penggantian', 'qty' => 3, 'days_ago' => 12, 'tech' => 'Budi'],
            ['room_idx' => 0, 'lamp_type_idx' => 3, 'type' => 'penggantian', 'qty' => 1, 'days_ago' => 18, 'tech' => 'Ahmad'],
            ['room_idx' => 12, 'lamp_type_idx' => 5, 'type' => 'pemasangan', 'qty' => 4, 'days_ago' => 25, 'tech' => 'Candra'],
            ['room_idx' => 7, 'lamp_type_idx' => 4, 'type' => 'penggantian', 'qty' => 2, 'days_ago' => 30, 'tech' => 'Budi'],
            ['room_idx' => 4, 'lamp_type_idx' => 2, 'type' => 'penggantian', 'qty' => 3, 'days_ago' => 35, 'tech' => 'Ahmad'],
            ['room_idx' => 18, 'lamp_type_idx' => 5, 'type' => 'pemasangan', 'qty' => 6, 'days_ago' => 40, 'tech' => 'Dani'],
            ['room_idx' => 8, 'lamp_type_idx' => 4, 'type' => 'penggantian', 'qty' => 1, 'days_ago' => 45, 'tech' => 'Budi'],
            ['room_idx' => 15, 'lamp_type_idx' => 4, 'type' => 'penggantian', 'qty' => 4, 'days_ago' => 50, 'tech' => 'Candra'],
            ['room_idx' => 3, 'lamp_type_idx' => 3, 'type' => 'penggantian', 'qty' => 2, 'days_ago' => 55, 'tech' => 'Ahmad'],
        ];

        foreach ($transactionData as $td) {
            Transaction::create([
                'room_id' => $rooms[$td['room_idx']]->id,
                'lamp_type_id' => $lampTypes[$td['lamp_type_idx']]->id,
                'type' => $td['type'],
                'quantity' => $td['qty'],
                'transaction_date' => Carbon::now()->subDays($td['days_ago']),
                'technician' => $td['tech'],
                'notes' => $td['type'] === 'penggantian' ? 'Penggantian lampu rusak' : 'Pemasangan lampu baru',
            ]);
        }

        // ============================================
        // 9. MAINTENANCE (Empty by default)
        // ============================================
        /*
        $maintenanceData = [];
        */
    }
}
