<?php

namespace App\Http\Controllers;

use App\Models\Lamp;
use App\Models\LampType;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Month & Year filter ────────────────────────────────────────────
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $selectedMonth = (int) request('month', now()->month);
        $selectedYear  = (int) request('year', now()->year);

        $availableYears = Maintenance::selectRaw('YEAR(COALESCE(completed_date, scheduled_date, created_at)) as yr')
            ->distinct()
            ->pluck('yr')
            ->filter()
            ->map(fn($y) => (int) $y)
            ->toArray();

        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
        }
        rsort($availableYears);

        // ── KPI Cards ──────────────────────────────────────────────────────
        $totalTitikLampu   = Lamp::count();
        $lampuTerpasang    = Lamp::whereIn('status', ['on', 'warning'])->count();
        
        $lampuDigantiBulan = Maintenance::where('type', 'penggantian')
            ->where(function ($q) use ($selectedMonth, $selectedYear) {
                $q->where(function ($sub) use ($selectedMonth, $selectedYear) {
                    $sub->whereMonth('completed_date', $selectedMonth)
                        ->whereYear('completed_date', $selectedYear);
                })->orWhere(function ($sub) use ($selectedMonth, $selectedYear) {
                    $sub->whereNull('completed_date')
                        ->whereMonth('created_at', $selectedMonth)
                        ->whereYear('created_at', $selectedYear);
                });
            })
            ->count();

        // ── Donut Chart – Status Lampu ──────────────────────────────────
        $statusCounts = Lamp::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusLabels = ['on', 'off', 'rusak', 'warning'];
        $statusNames  = ['Aktif', 'Mati', 'Rusak', 'Warning'];
        $statusData   = array_map(fn($s) => $statusCounts[$s] ?? 0, $statusLabels);

        // ── Tabel: Maintenance Terbaru ─────────────────────────────────────
        $recentMaintenances = Maintenance::with(['floor.building', 'lamp.lampType'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('pages.dashboard.admin', compact(
            'totalTitikLampu',
            'lampuTerpasang',
            'lampuDigantiBulan',
            'statusNames',
            'statusData',
            'recentMaintenances',
            'months',
            'availableYears',
            'selectedMonth',
            'selectedYear'
        ) + ['title' => 'Dashboard']);
    }
}
