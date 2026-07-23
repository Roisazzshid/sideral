<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Floor;
use App\Models\LampType;
use App\Models\Lamp;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Transaction;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FmLightningSeeder extends Seeder
{
    private $lampCounter = 1;

    public function run(): void
    {
        // Disable foreign key constraints and truncate existing tables for clean seed IDs
        Schema::disableForeignKeyConstraints();
        Building::truncate();
        Floor::truncate();
        LampType::truncate();
        Lamp::truncate();
        Inventory::truncate();
        InventoryTransaction::truncate();
        Transaction::truncate();
        Maintenance::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Create Building
        $building = Building::create([
            'name' => 'Gedung SIDERAL',
            'location' => 'Jakarta Utama',
            'description' => 'Gedung Operasional & Kantor Pusat SIDERAL',
        ]);

        // 2. Create Floors
        $floors = [
            'L1' => Floor::create(['building_id' => $building->id, 'name' => 'Lantai 1', 'floor_number' => 1]),
            'L2' => Floor::create(['building_id' => $building->id, 'name' => 'Lantai 2', 'floor_number' => 2]),
            'L3' => Floor::create(['building_id' => $building->id, 'name' => 'Lantai 3', 'floor_number' => 3]),
            'LPH' => Floor::create(['building_id' => $building->id, 'name' => 'Lantai PH B', 'floor_number' => 4]),
        ];

        // 3. Create Lamp Types (matching the plan legend exactly)
        $lampTypes = [
            'halogen' => LampType::create([
                'name' => 'Lampu Halogen LED 5 Watt',
                'type' => 'Halogen',
                'shape' => 'segitiga',
                'watt' => 5,
                'price' => 25000,
                'status' => 'aktif'
            ]),
            'downlight' => LampType::create([
                'name' => 'Lampu Down Light Bulb 12 Watt',
                'type' => 'Downlight',
                'shape' => 'bulat',
                'watt' => 12,
                'price' => 35000,
                'status' => 'aktif'
            ]),
            't5' => LampType::create([
                'name' => 'Lampu T5 16 Watt LED x 1 Pcs',
                'type' => 'T5',
                'shape' => 'garis',
                'watt' => 16,
                'price' => 50000,
                'status' => 'aktif'
            ]),
            'tl_1' => LampType::create([
                'name' => 'Lampu TL 16 Watt LED x 1 Pcs',
                'type' => 'TL 1',
                'shape' => 'garis',
                'watt' => 16,
                'price' => 60000,
                'status' => 'aktif'
            ]),
            'tl_2' => LampType::create([
                'name' => 'Lampu TL 16 Watt LED x 2 Pcs',
                'type' => 'TL 2',
                'shape' => 'persegi_panjang',
                'watt' => 16,
                'price' => 75000,
                'status' => 'aktif'
            ]),
        ];

        // 4. Seed Lamps directly per Floor (distributing them relative to the floor plan coordinate spaces)
        
        // --- LANTAI 1 (denah dengan R. Diskusi, R. Server, dll. - Rotasi 90 Derajat CCW) ---
        // Area R. DISKUSI: 4 Downlights
        $this->seedLampsInGrid($floors['L1'], $lampTypes['downlight'], 4, 43, 80, 53, 86);
        // Area R. SERVER: 2 T5 (horizontal)
        $this->seedLampsInGrid($floors['L1'], $lampTypes['t5'], 2, 43, 70, 53, 76, 0);
        // Area R. PANEL: 1 Downlight
        $this->seedLampsInGrid($floors['L1'], $lampTypes['downlight'], 1, 58, 82, 65, 86);
        // Area TOILET PRIA: 2 Downlights
        $this->seedLampsInGrid($floors['L1'], $lampTypes['downlight'], 2, 58, 64, 63, 68);
        // Area TOILET WANITA: 2 Downlights
        $this->seedLampsInGrid($floors['L1'], $lampTypes['downlight'], 2, 70, 64, 80, 68);
        // Area R. SORTIR: 2 TL 1 Pcs (horizontal)
        $this->seedLampsInGrid($floors['L1'], $lampTypes['tl_1'], 2, 43, 32, 53, 40, 0);
        // Area GUDANG BIST/UKP/OSS: 2 TL 2 Pcs (horizontal)
        $this->seedLampsInGrid($floors['L1'], $lampTypes['tl_2'], 2, 43, 8, 53, 22, 0);
        // Area Utama (Selasar/Open Space): 24 TL 1 Pcs (vertical, 6 columns x 4 rows)
        $this->seedLampsInGrid($floors['L1'], $lampTypes['tl_1'], 24, 8, 25, 32, 95, 90, 6);
        // Area Top-Right Room: 4 Downlights & 1 Halogen
        $this->seedLampsInGrid($floors['L1'], $lampTypes['downlight'], 4, 10, 8, 22, 18);
        $this->seedLampsInGrid($floors['L1'], $lampTypes['halogen'], 1, 6, 6, 8, 8);
        // Area below Top-Right Room: 2 TL 2 Pcs (vertical)
        $this->seedLampsInGrid($floors['L1'], $lampTypes['tl_2'], 2, 30, 8, 40, 18, 90);
        // Halogen in the middle hallway
        $this->seedLampsInGrid($floors['L1'], $lampTypes['halogen'], 4, 35, 50, 38, 60);

        // --- LANTAI 2 ---
        // Area Musolah: 4 Downlights
        $this->seedLampsInGrid($floors['L2'], $lampTypes['downlight'], 4, 18, 62, 23, 74);
        // Area Toilet Pria: 4 Downlights
        $this->seedLampsInGrid($floors['L2'], $lampTypes['downlight'], 4, 25, 62, 29, 74);
        // Area Toilet Wanita: 4 Downlights
        $this->seedLampsInGrid($floors['L2'], $lampTypes['downlight'], 4, 31, 62, 35, 74);
        // Area Kerja Utama: 40 Downlights & 20 T5
        $this->seedLampsInGrid($floors['L2'], $lampTypes['downlight'], 40, 5, 8, 95, 48);
        $this->seedLampsInGrid($floors['L2'], $lampTypes['t5'], 20, 5, 50, 95, 58, 90);

        // --- LANTAI 3 ---
        // Area Recovery: 4 Downlights
        $this->seedLampsInGrid($floors['L3'], $lampTypes['downlight'], 4, 10, 45, 20, 60);
        // Area Musolah: 4 Downlights
        $this->seedLampsInGrid($floors['L3'], $lampTypes['downlight'], 4, 10, 65, 20, 78);
        // Area R. Loker: 4 Downlights
        $this->seedLampsInGrid($floors['L3'], $lampTypes['downlight'], 4, 10, 80, 20, 95);
        // Area Ruang UPS: 4 Downlights
        $this->seedLampsInGrid($floors['L3'], $lampTypes['downlight'], 4, 16, 62, 23, 70);
        // Area Toilet Pria: 4 Downlights
        $this->seedLampsInGrid($floors['L3'], $lampTypes['downlight'], 4, 24, 62, 28, 74);
        // Area Toilet Wanita: 4 Downlights
        $this->seedLampsInGrid($floors['L3'], $lampTypes['downlight'], 4, 30, 62, 34, 74);
        // Area R. Hub: 2 T5
        $this->seedLampsInGrid($floors['L3'], $lampTypes['t5'], 2, 35, 62, 40, 74, 90);
        // Area Kerja Utama: 60 Downlights & 30 T5
        $this->seedLampsInGrid($floors['L3'], $lampTypes['downlight'], 60, 5, 8, 95, 48);
        $this->seedLampsInGrid($floors['L3'], $lampTypes['t5'], 30, 5, 50, 95, 58, 90);

        // --- LANTAI PH B ---
        // Area R. QA: 8 TL 1 Pcs
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['tl_1'], 8, 5, 10, 16, 48, 90);
        // Area Class Aplikasi Besar: 6 TL 1 Pcs
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['tl_1'], 6, 18, 10, 30, 43, 90);
        // Area R. Smarty: 4 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 4, 32, 10, 38, 38);
        // Area R. Studio: 4 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 4, 46, 10, 54, 38);
        // Area R. Voh: 4 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 4, 58, 10, 68, 38);
        // Area R. Fanction: 6 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 6, 70, 10, 80, 38);
        // Area Toilet Pria: 4 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 4, 18, 64, 23, 75);
        // Area Toilet Wanita: 4 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 4, 25, 64, 29, 75);
        // Area R. Hub: 2 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 2, 31, 64, 35, 75);
        // Area R. Roll Play: 6 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 6, 45, 64, 55, 75);
        // Area R. Quick: 8 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 8, 60, 64, 70, 75);
        // Area R. Aplikasi Kecil: 8 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 8, 60, 77, 70, 88);
        // Area R. BM PT.DPI: 4 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 4, 12, 82, 17, 95);
        // Area Roof Garden: 12 Downlights
        $this->seedLampsInGrid($floors['LPH'], $lampTypes['downlight'], 12, 82, 45, 95, 85);

        // 5. Seed Inventory
        $inventories = [];
        $inventories['halogen'] = Inventory::create(['lamp_type_id' => $lampTypes['halogen']->id, 'stock_quantity' => 150, 'min_stock' => 15]);
        $inventories['downlight'] = Inventory::create(['lamp_type_id' => $lampTypes['downlight']->id, 'stock_quantity' => 200, 'min_stock' => 20]);
        $inventories['t5'] = Inventory::create(['lamp_type_id' => $lampTypes['t5']->id, 'stock_quantity' => 85, 'min_stock' => 10]);
        $inventories['tl_1'] = Inventory::create(['lamp_type_id' => $lampTypes['tl_1']->id, 'stock_quantity' => 100, 'min_stock' => 10]);
        $inventories['tl_2'] = Inventory::create(['lamp_type_id' => $lampTypes['tl_2']->id, 'stock_quantity' => 90, 'min_stock' => 10]);

        // 6. Seed Inventory Transactions
        foreach ($inventories as $inv) {
            for ($i = 0; $i < 3; $i++) {
                InventoryTransaction::create([
                    'inventory_id' => $inv->id,
                    'type' => 'masuk',
                    'quantity' => rand(20, 100),
                    'transaction_date' => Carbon::now()->subDays(rand(5, 120)),
                    'reference' => 'PO-' . rand(1000, 9999),
                    'notes' => 'Restock awal dari supplier',
                ]);
            }
        }

        // 7. Seed Transactions History
        $floorsArray = Floor::all();
        foreach ($floorsArray as $fl) {
            for ($i = 0; $i < 5; $i++) {
                $randomType = array_rand($lampTypes);
                Transaction::create([
                    'floor_id' => $fl->id,
                    'lamp_type_id' => $lampTypes[$randomType]->id,
                    'type' => 'pemasangan',
                    'quantity' => rand(1, 4),
                    'transaction_date' => Carbon::now()->subDays(rand(1, 60)),
                    'technician' => 'Ahmad',
                    'notes' => 'Pemasangan unit baru saat setup lantai',
                ]);
            }
        }
    }

    /**
     * Helper to calculate a clean grid layout of lamps inside floor coordinates
     */
    private function seedLampsInGrid(Floor $floor, LampType $type, int $count, float $startX, float $startY, float $endX, float $endY, int $rotation = 0, int $customCols = null): void
    {
        $positions = [];
        $cols = $customCols ?: ceil(sqrt($count));
        $rows = ceil($count / $cols);

        $stepX = $cols > 1 ? ($endX - $startX) / ($cols - 1) : 0;
        $stepY = $rows > 1 ? ($endY - $startY) / ($rows - 1) : 0;

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if (count($positions) >= $count) break 2;
                $x = $startX + ($c * $stepX);
                $y = $startY + ($r * $stepY);
                $positions[] = ['x' => round($x, 2), 'y' => round($y, 2)];
            }
        }

        foreach ($positions as $pos) {
            $w = 20;
            $h = 20;
            if ($type->type === 'T5') {
                $w = 32;
                $h = 4;
            } elseif ($type->type === 'TL 1') {
                $w = 32;
                $h = 6;
            } elseif ($type->type === 'TL 2') {
                $w = 32;
                $h = 14;
            }

            Lamp::create([
                'floor_id' => $floor->id,
                'lamp_type_id' => $type->id,
                'code' => 'L-' . str_pad($this->lampCounter++, 4, '0', STR_PAD_LEFT),
                'position_x' => $pos['x'],
                'position_y' => $pos['y'],
                'rotation' => $rotation,
                'width' => $w,
                'height' => $h,
                'status' => 'on', // status langsung aktif (warna hijau)
                'installed_date' => Carbon::now()->subDays(rand(10, 180)),
            ]);
        }
    }
}
