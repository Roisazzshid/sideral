@extends('layouts.app')

@section('content')

{{-- ─────────────────────────────────────────────────────────────
     KPI CARDS
───────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 mb-6">

    {{-- Total Titik Lampu --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-900/30">
                <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full dark:bg-teal-900/30 dark:text-teal-400">
                Titik
            </span>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Titik Lampu</p>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ number_format($totalTitikLampu) }}
            <span class="text-base font-normal text-gray-500 dark:text-gray-400">Titik</span>
        </h3>
    </div>

    {{-- Lampu Diganti --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 flex flex-col justify-between">
        <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-900/30">
                    <svg class="w-6 h-6 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 rounded-xl border border-gray-200 bg-gray-50/80 px-2 py-1 dark:border-gray-700 dark:bg-gray-900/50 shadow-inner">
                    <select name="month" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none dark:text-gray-200 cursor-pointer pr-1">
                        @foreach($months as $mNum => $mName)
                            <option value="{{ $mNum }}" @selected($selectedMonth == $mNum)>{{ $mName }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs text-gray-300 dark:text-gray-600 font-bold">•</span>
                    <select name="year" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none dark:text-gray-200 cursor-pointer pl-1">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" @selected($selectedYear == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Lampu Diganti ({{ $months[$selectedMonth] ?? '' }} {{ $selectedYear }})</p>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ number_format($lampuDigantiBulan) }}
            <span class="text-base font-normal text-gray-500 dark:text-gray-400">Buah</span>
        </h3>
    </div>

</div>

{{-- ─────────────────────────────────────────────────────────────
     ROW 2: DONUT CHART + TRANSAKSI TERBARU
───────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6 items-start">

    {{-- Donut Chart – Status Lampu --}}
    <div class="xl:col-span-1 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-hidden">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Status Lampu</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Distribusi kondisi saat ini</p>
        </div>
        <div id="dashStatusDonut" class="flex justify-center"></div>
        <ul class="mt-4 space-y-2">
            @php
                $donutColors = ['#22c55e', '#6b7280', '#ef4444', '#f59e0b'];
            @endphp
            @foreach($statusNames as $i => $label)
                <li class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $donutColors[$i] }}"></span>
                        {{ $label }}
                    </span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($statusData[$i]) }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Tabel Maintenance Terbaru --}}
    <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Maintenance Terbaru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Log pemeliharaan & penggantian lampu</p>
            </div>
            <a href="{{ route('maintenance') }}"
               class="text-xs font-medium text-teal-600 hover:text-teal-700 dark:text-teal-400 hover:underline">
                Lihat semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 text-xs">
                        <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">Kode Lampu / Area</th>
                        <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                        <th class="pb-3 font-medium text-gray-500 dark:text-gray-400 text-right">Teknisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700 text-xs">
                    @forelse($recentMaintenances as $mt)
                        @php
                            $d = $mt->completed_date ?: ($mt->scheduled_date ?: $mt->created_at);
                            $dateStr = $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '-';
                            $lampCode = $mt->lamp ? $mt->lamp->code : ($mt->lamp_id ? 'L-' . $mt->lamp_id : '-');
                            $roomName = $mt->room ? $mt->room->name : '-';
                        @endphp
                        <tr>
                            <td class="py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $dateStr }}
                            </td>
                            <td class="py-3 font-medium text-gray-800 dark:text-gray-200">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $lampCode }}</div>
                                <div class="text-[11px] text-gray-400 font-normal">{{ $roomName }}</div>
                            </td>
                            <td class="py-3">
                                @if(strtolower($mt->type) === 'penggantian')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                        Penggantian
                                    </span>
                                @elseif(strtolower($mt->type) === 'perbaikan')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400">
                                        Perbaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ ucfirst($mt->type ?: 'Maintenance') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $mt->assigned_to ?: 'Teknisi' }}</div>
                                <div class="text-[10px] text-gray-400 capitalize">{{ $mt->status }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400">Belum ada data maintenance</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<style>
/* Pastikan chart tidak overflow keluar card */
#dashStatusDonut {
    position: relative;
    z-index: 0;
}
/* Tooltip ApexCharts tetap di atas tapi tidak melewati card lain */
.apexcharts-tooltip {
    z-index: 10 !important;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Colour helpers ──────────────────────────────────────────
    const isDark = () => document.documentElement.classList.contains('dark');
    const labelColor = () => isDark() ? '#9ca3af' : '#6b7280';

    // ── Donut Chart – Status Lampu ───────────────────────────
    const statusLabels = @json($statusNames);
    const statusData   = @json($statusData);
    const donutColors  = ['#22c55e', '#6b7280', '#ef4444', '#f59e0b'];
    const totalLamps   = statusData.reduce((a, b) => a + b, 0);

    const donutOptions = {
        series: statusData,
        chart: {
            type: 'donut',
            height: 200,
            parentHeightOffset: 0,
            background: 'transparent',
            fontFamily: 'inherit',
        },
        labels: statusLabels,
        colors: donutColors,
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            color: labelColor(),
                            fontSize: '13px',
                            fontWeight: 500,
                            formatter: () => totalLamps.toLocaleString('id-ID'),
                        },
                        value: {
                            fontSize: '20px',
                            fontWeight: 700,
                            color: isDark() ? '#f9fafb' : '#111827',
                        },
                    },
                },
            },
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: { width: 0 },
        tooltip: {
            theme: isDark() ? 'dark' : 'light',
            y: { formatter: (v) => v + ' lampu' },
        },
    };

    const donutChart = new ApexCharts(document.querySelector('#dashStatusDonut'), donutOptions);
    donutChart.render();

    // ── Re-render on theme change ───────────────────────────────
    const observer = new MutationObserver(() => {
        donutChart.updateOptions({
            tooltip: { theme: isDark() ? 'dark' : 'light' },
        });
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

});
</script>
@endpush
