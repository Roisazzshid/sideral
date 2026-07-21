<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Lamp;
use App\Models\LampType;
use App\Models\Transaction;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m')); // e.g. 2026-07
        $buildingId = $request->query('building_id');

        try {
            $carbonPeriod = Carbon::createFromFormat('Y-m', $period);
        } catch (\Throwable $e) {
            $carbonPeriod = now();
            $period = now()->format('Y-m');
        }

        $year = $carbonPeriod->year;
        $month = $carbonPeriod->month;
        $monthName = $carbonPeriod->translatedFormat('F Y');

        // Fetch buildings
        $buildings = Building::orderBy('name')->get();
        $selectedBuilding = $buildingId ? Building::find($buildingId) : null;

        // Calculate Lamp Usage Data
        $usageData = $this->calculateUsageReport($year, $month, $buildingId);

        return view('pages.sideral.reports', [
            'title'            => 'Report',
            'period'           => $period,
            'buildingId'       => $buildingId,
            'monthName'        => $monthName,
            'buildings'        => $buildings,
            'selectedBuilding' => $selectedBuilding,
            'usage'            => $usageData,
        ]);
    }

    public function export(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));
        $buildingId = $request->query('building_id');

        try {
            $carbonPeriod = Carbon::createFromFormat('Y-m', $period);
        } catch (\Throwable $e) {
            $carbonPeriod = now();
            $period = now()->format('Y-m');
        }

        $year = $carbonPeriod->year;
        $month = $carbonPeriod->month;

        $filename = "laporan_pemakaian_lampu_{$period}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($year, $month, $buildingId) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Laporan Pemakaian & Penggantian Lampu - Periode ' . $month . '/' . $year]);
            fputcsv($file, []);
            fputcsv($file, ['No', 'Tanggal', 'Kode Lampu', 'Jenis Lampu', 'Model/Bentuk', 'Lokasi (Gedung/Lantai)', 'Jumlah (Unit)', 'Teknisi', 'Keterangan']);

            $usageData = $this->calculateUsageReport($year, $month, $buildingId);
            foreach ($usageData['list'] as $idx => $item) {
                fputcsv($file, [
                    $idx + 1,
                    $item['date'],
                    $item['lamp_code'],
                    $item['lamp_type_name'],
                    $item['shape'],
                    $item['location'],
                    $item['quantity'],
                    $item['technician'],
                    $item['notes'],
                ]);
            }
            fputcsv($file, []);
            fputcsv($file, ['Ringkasan Pemakaian Lampu']);
            fputcsv($file, ['Total Pemakaian/Penggantian (Unit)', $usageData['totalQuantity']]);
            fputcsv($file, ['Jenis Lampu Paling Banyak Dipakai', $usageData['topLampType']]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateUsageReport($year, $month, $buildingId)
    {
        // Fetch transactions (penggantian & pemasangan)
        $txQuery = Transaction::with(['lampType', 'room.floor.building', 'lamp'])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($buildingId) {
            $txQuery->whereHas('room.floor', fn($q) => $q->where('building_id', $buildingId));
        }

        $transactions = $txQuery->orderBy('transaction_date', 'desc')->get();

        $list = [];
        $lampTypeCounts = [];
        $totalQty = 0;
        $replacementCount = 0;
        $installationCount = 0;

        foreach ($transactions as $tx) {
            $qty = $tx->quantity ?: 1;
            $totalQty += $qty;

            if ($tx->type === 'pemasangan') {
                $installationCount += $qty;
            } else {
                $replacementCount += $qty;
            }

            $typeName = $tx->lampType?->name ?? 'Lampu';
            $shape = ($tx->lampType?->shape === 'panjang') ? 'Panjang ▬' : 'Bulat ⚪';
            
            $lampTypeCounts[$typeName] = ($lampTypeCounts[$typeName] ?? 0) + $qty;

            $buildingName = $tx->room?->floor?->building?->name ?? '-';
            $floorName = $tx->room?->floor?->name ?? '-';
            $location = $buildingName . ' / ' . $floorName;

            $list[] = [
                'date'           => $tx->transaction_date?->format('d/m/Y') ?: '-',
                'lamp_code'      => $tx->lamp?->code ?: '-',
                'lamp_type_name' => $typeName,
                'shape'          => $shape,
                'location'       => $location,
                'quantity'       => $qty,
                'technician'     => $tx->technician ?: '-',
                'notes'          => $tx->notes ?: 'Penggantian/Pemakaian lampu',
            ];
        }

        // Top used lamp type
        arsort($lampTypeCounts);
        $topLampType = !empty($lampTypeCounts) ? array_key_first($lampTypeCounts) . ' (' . reset($lampTypeCounts) . ' unit)' : '-';

        // Breakdown per lamp type array
        $byType = [];
        foreach ($lampTypeCounts as $name => $count) {
            $pct = $totalQty > 0 ? round(($count / $totalQty) * 100, 1) : 0;
            $byType[] = [
                'name'       => $name,
                'quantity'   => $count,
                'percentage' => $pct,
            ];
        }

        return [
            'list'              => $list,
            'totalQuantity'     => $totalQty,
            'replacementCount'  => $replacementCount,
            'installationCount' => $installationCount,
            'topLampType'       => $topLampType,
            'byType'            => $byType,
        ];
    }
}
