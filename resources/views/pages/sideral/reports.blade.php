@extends('layouts.app')

@section('content')
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            aside, header, .no-print, button, form, .tabs-nav {
                display: none !important;
            }
            .xl\:ml-\[290px\], .xl\:ml-\[90px\] {
                margin-left: 0 !important;
            }
            .p-4.mx-auto {
                padding: 0 !important;
            }
            .min-h-screen {
                min-height: auto !important;
            }
            .rounded-lg, .rounded-2xl {
                border-radius: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }
            .border {
                border: none !important;
            }
            .shadow-sm, .shadow-xl {
                box-shadow: none !important;
            }
            #reportPrintContent {
                visibility: visible;
                display: block !important;
                width: 100% !important;
            }
            .overflow-x-auto {
                overflow: visible !important;
            }
            table {
                width: 100% !important;
                table-layout: auto !important;
                font-size: 10px !important;
            }
            th, td {
                white-space: normal !important;
                word-wrap: break-word !important;
                padding: 6px 4px !important;
            }
            .whitespace-nowrap {
                white-space: normal !important;
            }
        }
    </style>

    <x-common.page-breadcrumb pageTitle="Report" class="no-print" />

    <div class="space-y-5">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 no-print">
                {{ session('success') }}
            </div>
        @endif
        <!-- Top Controls / Filter Bar -->
        <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white px-5 py-4 lg:flex-row lg:items-center lg:justify-between no-print">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Pemakaian & Penggantian Lampu</h3>
                <p class="mt-1 text-sm text-gray-500">Analisis pemakaian, penggantian, dan konsumsi fisik unit lampu fasilitas.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.export', ['period' => $period, 'building_id' => $buildingId]) }}" 
                   class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </a>
                <button type="button" onclick="window.print()" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Cetak PDF
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('reports') }}" class="rounded-lg border border-gray-200 bg-white px-5 py-4 no-print">
            <div class="grid gap-3 md:grid-cols-3 items-end">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Periode Bulan</label>
                    <input type="month" name="period" value="{{ $period }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Gedung</label>
                    <select name="building_id" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                        <option value="">Semua Gedung</option>
                        @foreach($buildings as $b)
                            <option value="{{ $b->id }}" @selected($buildingId == $b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="h-10 w-full rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- PRINTABLE CONTENT CONTAINER -->
        <div id="reportPrintContent" class="space-y-5">
            
            <!-- Report Header Info for Print -->
            <div class="hidden print:block border-b border-gray-300 pb-4 mb-4">
                <h1 class="text-2xl font-bold text-gray-900">LAPORAN PEMAKAIAN & PENGGANTIAN LAMPU</h1>
                <p class="text-sm text-gray-600">Periode: {{ $monthName }} | Gedung: {{ $selectedBuilding ? $selectedBuilding->name : 'Semua Gedung' }}</p>
            </div>

            <!-- Tabel Detail Transaksi Pemakaian / Penggantian -->
            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h4 class="font-semibold text-gray-800">Daftar Transaksi Pemakaian & Penggantian Lampu</h4>
                    <p class="mt-1 text-xs text-gray-500">Rincian riwayat pemasangan dan penggantian titik lampu pada fasilitas.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                            <tr>
                                <th class="w-12 px-5 py-3">No</th>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Kode Lampu</th>
                                <th class="px-5 py-3">Jenis Lampu</th>
                                <th class="px-5 py-3">Model</th>
                                <th class="px-5 py-3">Lokasi</th>
                                <th class="px-5 py-3 text-right">Jumlah</th>
                                <th class="px-5 py-3">Teknisi</th>
                                <th class="px-5 py-3">Keterangan</th>
                                <th class="px-5 py-3 text-right no-print">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($usage['list'] as $idx => $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3.5 text-gray-500">{{ $idx + 1 }}</td>
                                    <td class="px-5 py-3.5 font-medium text-gray-700 whitespace-nowrap">{{ $row['date'] }}</td>
                                    <td class="px-5 py-3.5 font-bold text-teal-700 whitespace-nowrap">{{ $row['lamp_code'] }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-gray-800 whitespace-nowrap">{{ $row['lamp_type_name'] }}</td>
                                    <td class="px-5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $row['shape'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 whitespace-nowrap">{{ $row['location'] }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-900 whitespace-nowrap">{{ number_format($row['quantity']) }} Unit</td>
                                    <td class="px-5 py-3.5 text-gray-700 whitespace-nowrap">{{ $row['technician'] }}</td>
                                    <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $row['notes'] }}</td>
                                    <td class="px-5 py-3.5 text-right no-print">
                                        <form method="POST" action="{{ route('transactions.destroy', $row['id']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok inventaris akan dikembalikan.');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-100 bg-red-50 text-red-600 hover:bg-red-100" title="Hapus Transaksi">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-12 text-center text-sm text-gray-400 italic">
                                        Tidak ada catatan transaksi pemakaian atau penggantian lampu pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
