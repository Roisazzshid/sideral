@extends('layouts.app')
<!-- CacheBuster: {{ time() }} -->

@section('content')
    <style>
        #fpPanel::-webkit-scrollbar {
            width: 5px;
        }
        #fpPanel::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        #fpPanel::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        #fpPanel::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            /* Hide all UI layout elements */
            aside, header, nav, .no-print, button, form, iframe, #fpPanel, #fpCanvas, .mb-4, .flex-col.gap-4, div[style*="min-height: 620px"] {
                display: none !important;
            }
            /* Display ONLY print report container */
            #fpPrintReportView {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>

    <x-common.page-breadcrumb pageTitle="Floor Plan" class="no-print" />

    <div class="mb-4 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3">
    <div class="flex items-center gap-2">
        <label for="buildingSelect" class="text-sm font-medium text-gray-600">Gedung</label>
        <select id="buildingSelect" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
            @foreach($buildings as $building)
                <option value="{{ $building->id }}" @selected($selectedBuilding && $selectedBuilding->id === $building->id)>
                    {{ $building->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="h-6 w-px bg-gray-200"></div>

    <div class="flex items-center gap-2">
        <label for="floorSelect" class="text-sm font-medium text-gray-600">Lantai</label>
        <select id="floorSelect" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
            @if($selectedBuilding)
                @foreach($selectedBuilding->floors as $floor)
                    <option value="{{ $floor->id }}" @selected($selectedFloor && $selectedFloor->id === $floor->id)>
                        {{ $floor->name }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="flex-1"></div>

    <button id="btnEditMode" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        <span id="editModeLabel">+ Edit Titik Lampu</span>
    </button>
    <label class="cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        Upload Denah
        <input id="uploadInput" type="file" accept="image/*" class="hidden">
    </label>
    <button id="btnExportPdf" type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-teal-700 bg-teal-700 px-3.5 py-2 text-sm font-semibold text-white hover:bg-teal-800 shadow-sm">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        Export PDF
    </button>
</div>

<div class="flex flex-col gap-4 xl:flex-row items-start" style="min-height: 620px;">
    <aside id="fpPanel" class="w-full flex-shrink-0 rounded-lg border border-gray-200 bg-white p-4 xl:w-72 xl:max-h-[calc(100vh-175px)] xl:overflow-y-auto shadow-sm sticky top-4">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase text-gray-500">Status Lampu</p>
            <div class="space-y-2">
                <label class="flex items-center justify-between gap-2 text-sm text-gray-700">
                    <span class="flex items-center gap-2">
                        <input type="checkbox" class="fp-status-filter rounded accent-green-500" value="on" checked>
                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span> Aktif
                    </span>
                    <span id="cnt-on" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">0</span>
                </label>
                <label class="flex items-center justify-between gap-2 text-sm text-gray-700">
                    <span class="flex items-center gap-2">
                        <input type="checkbox" class="fp-status-filter rounded accent-gray-500" value="off" checked>
                        <span class="h-2.5 w-2.5 rounded-full bg-gray-600"></span> Mati
                    </span>
                    <span id="cnt-off" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">0</span>
                </label>
                <label class="flex items-center justify-between gap-2 text-sm text-gray-700">
                    <span class="flex items-center gap-2">
                        <input type="checkbox" class="fp-status-filter rounded accent-orange-500" value="warning" checked>
                        <span class="h-2.5 w-2.5 rounded-full bg-orange-500"></span> Warning
                    </span>
                    <span id="cnt-warning" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">0</span>
                </label>
                <label class="flex items-center justify-between gap-2 text-sm text-gray-700">
                    <span class="flex items-center gap-2">
                        <input type="checkbox" class="fp-status-filter rounded accent-red-500" value="rusak" checked>
                        <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span> Error
                    </span>
                    <span id="cnt-rusak" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">0</span>
                </label>
            </div>
        </div>

        <hr class="my-4 border-gray-100">

        <div>
            <p class="mb-2 text-xs font-semibold uppercase text-gray-500">Pencarian</p>
            <input id="fpSearch" type="text" placeholder="Cari kode / jenis lampu..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        <hr class="my-4 border-gray-100">

        <div>
            <p class="mb-2 text-xs font-semibold uppercase text-gray-500">Filter History Penggantian</p>
            <div class="space-y-2">
                <button id="btnOpenCalendarModal" type="button" class="w-full flex items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-700 hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-sm">
                    <span id="calendarTriggerLabel" class="font-medium text-gray-600">Pilih Range Tanggal (Kalender)...</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-teal-700">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </button>
                <div class="flex gap-2">
                    <button id="btnResetDateFilter" type="button" class="w-full rounded-lg border border-gray-300 bg-white py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">
                        Reset Filter Tanggal
                    </button>
                </div>
                <div id="filterResultBadge" class="hidden rounded-lg bg-amber-50 px-2.5 py-1.5 text-xs text-amber-900 border border-amber-200">
                    💡 Ditemukan <span id="filteredLampCount" class="font-bold">0</span> titik diganti pada rentang tanggal tersebut.
                </div>
            </div>
        </div>

        <hr class="my-4 border-gray-100">

        <div>
            <p class="mb-2 text-xs font-semibold uppercase text-gray-500">Layer</p>
            <div class="space-y-2 text-sm text-gray-700">
                <label class="flex items-center gap-2">
                    <input id="layerLamps" type="checkbox" class="rounded accent-teal-500" checked>
                    Titik Lampu
                </label>
                <label class="flex items-center gap-2">
                    <input id="layerFloor" type="checkbox" class="rounded accent-teal-500" checked>
                    Denah Lantai
                </label>
            </div>
        </div>

        <hr class="my-4 border-gray-100">

        <button id="btnResetView" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Reset View
        </button>

        <div class="mt-4 rounded-lg bg-gray-50 p-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Total Titik</span>
                <span id="statTotal" class="font-semibold text-gray-800">0</span>
            </div>
            <div class="mt-1 flex items-center justify-between text-sm">
                <span class="text-gray-500">Aktif</span>
                <span id="statAktif" class="font-semibold text-green-600">0</span>
            </div>
            <div class="mt-1 flex items-center justify-between text-sm">
                <span class="text-gray-500">Bermasalah</span>
                <span id="statMasalah" class="font-semibold text-red-600">0</span>
            </div>
        </div>
    </aside>

    <section id="fpCanvas" class="relative min-h-[600px] flex-1 overflow-hidden rounded-lg border border-gray-200 bg-slate-100" style="cursor: grab;">
        <div class="absolute right-3 top-3 z-20 flex items-center gap-1">
            <button id="btnZoomIn" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50" title="Zoom in">+</button>
            <button id="btnZoomOut" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50" title="Zoom out">-</button>
        </div>

        <div id="zoomBadge" class="absolute left-3 top-3 z-20 rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-semibold text-gray-600 shadow-sm">100%</div>
        <div id="editBadge" class="pointer-events-none absolute bottom-3 left-3 z-20 hidden rounded-lg bg-teal-600 px-3 py-2 text-xs font-semibold text-white shadow-md">
            Mode edit aktif. Drag titik untuk pindah, klik kanvas kosong untuk tambah titik.
        </div>

        <div id="fpInner" class="absolute inset-0" style="transform-origin: top left; will-change: transform;">
            <div id="fpFloorLayer" class="absolute inset-0"></div>
            <div id="fpDotsLayer" class="absolute inset-0"></div>
        </div>

        <div id="fpTooltip" class="absolute z-30 hidden w-72 rounded-lg border border-gray-200 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-3 py-2">
                <span id="ttCode" class="text-sm font-semibold text-gray-800"></span>
                <button id="ttClose" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="Tutup">x</button>
            </div>
            <div class="space-y-1 px-3 py-3">
                <div id="ttName" class="text-sm text-gray-700"></div>
                <div id="ttWatt" class="text-xs text-gray-500"></div>
                <span id="ttBadge" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"></span>
            </div>
            <div class="border-t border-gray-100 px-3 py-2.5 bg-gray-50/50">
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Histori Penggantian</div>
                <div id="ttHistoryList" class="space-y-1 max-h-24 overflow-y-auto no-scrollbar text-xs text-gray-600">
                    <!-- Dynamic history rows will be loaded here -->
                </div>
            </div>
            <div class="border-t border-gray-100 px-3 py-2 bg-slate-50 text-[11px] text-slate-500">
                <span class="font-semibold text-teal-700">⚡ Maintenance Sync:</span> Status lampu otomatis diperbarui berdasarkan tiket & pengerjaan maintenance.
            </div>
            <div class="border-t border-gray-100 px-3 py-3">
                <div id="ttRotationControl" class="mt-3 hidden space-y-2">
                    <label for="ttRotation" class="block text-xs font-medium text-gray-500">Rotasi (derajat)</label>
                    <div class="flex items-center gap-2">
                        <button id="ttRotateLeft" class="flex-1 rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">← 90°</button>
                        <input id="ttRotation" type="number" min="0" max="360" step="1" class="w-16 rounded-lg border border-gray-300 px-2 py-1 text-xs text-gray-700 text-center focus:outline-none focus:ring-2 focus:ring-teal-500" value="0">
                        <button id="ttRotateRight" class="flex-1 rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">90° →</button>
                    </div>
                    <button id="ttSaveRotation" class="w-full rounded-lg border border-teal-200 bg-teal-50 py-1 text-xs font-medium text-teal-700 hover:bg-teal-100">
                        Simpan Rotasi
                    </button>

                    <div class="pt-2 border-t border-gray-100 space-y-2">
                        <label class="block text-xs font-medium text-gray-500">Ukuran (Panjang × Lebar px)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="block text-[10px] text-gray-400">Panjang (px)</span>
                                <input id="ttWidth" type="number" min="10" max="200" class="w-full rounded-lg border border-gray-300 px-2 py-1 text-xs text-center text-gray-700 focus:ring-2 focus:ring-purple-200" value="32">
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400">Lebar (px)</span>
                                <input id="ttHeight" type="number" min="4" max="100" class="w-full rounded-lg border border-gray-300 px-2 py-1 text-xs text-center text-gray-700 focus:ring-2 focus:ring-purple-200" value="14">
                            </div>
                        </div>
                        <button id="ttSaveDimensions" class="w-full rounded-lg border border-purple-200 bg-purple-50 py-1 text-xs font-medium text-purple-700 hover:bg-purple-100">
                            Simpan Ukuran
                        </button>
                    </div>
                </div>
                <button id="ttDelete" class="mt-2 w-full rounded-lg border border-red-200 bg-red-50 py-2 text-xs font-medium text-red-600 hover:bg-red-100">
                    Hapus Titik Lampu
                </button>
            </div>
        </div>

        <div id="fpAddModal" class="absolute z-30 hidden w-72 rounded-lg border border-gray-200 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <span class="text-sm font-semibold text-gray-800">Tambah Titik Lampu</span>
                <button id="addModalClose" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="Tutup">x</button>
            </div>
            <div class="space-y-3 px-4 py-3">
                <div>
                    <label for="addLampType" class="mb-1 block text-xs font-medium text-gray-600">Jenis Lampu</label>
                    <select id="addLampType" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Pilih jenis lampu</option>
                        @foreach($lampTypes as $lampType)
                            <option value="{{ $lampType->id }}">{{ $lampType->name }} ({{ $lampType->watt }}W)</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="addPosX" class="mb-1 block text-xs font-medium text-gray-600">Posisi X</label>
                        <input id="addPosX" type="text" readonly class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                    </div>
                    <div>
                        <label for="addPosY" class="mb-1 block text-xs font-medium text-gray-600">Posisi Y</label>
                        <input id="addPosY" type="text" readonly class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                    </div>
                </div>
                <button id="addSaveBtn" class="w-full rounded-lg bg-teal-600 py-2 text-sm font-medium text-white hover:bg-teal-700">
                    Simpan Titik Lampu
                </button>
            </div>
        </div>
    </section>
</div>

<!-- PRINT VIEW CONTAINER (ACTIVE ON PRINT / EXPORT PDF) -->
<div id="fpPrintReportView" class="hidden print:block mb-6 space-y-4">
    <div class="border-b-2 border-teal-800 pb-3 flex justify-between items-end">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">LAPORAN DENAH & HISTORI PENGGANTIAN LAMPU</h1>
            <p class="text-xs text-gray-600">
                Gedung: <span id="printBuildingName" class="font-semibold text-gray-800">-</span> |
                Lantai: <span id="printFloorName" class="font-semibold text-gray-800">-</span> |
                Filter Tanggal: <span id="printFilterDateLabel" class="font-semibold text-teal-700">Semua Tanggal</span>
            </p>
        </div>
        <div class="text-right text-[11px] text-gray-500">
            Cetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="grid grid-cols-12 gap-4 items-start">
        <!-- Left: Canvas Container (7 cols) -->
        <div class="col-span-7 rounded-lg border border-gray-300 p-2 bg-white shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-2 border-b border-gray-100 pb-1">Denah Lantai & Titik Lampu</h3>
            <div id="printCanvasContainer" class="relative overflow-hidden rounded border border-gray-200 bg-slate-50" style="min-height: 420px;">
                <!-- Cloned floor plan & dots rendered here for print -->
            </div>
        </div>

        <!-- Right: History List (5 cols) -->
        <div class="col-span-5 rounded-lg border border-gray-300 p-3 bg-white shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-2 border-b border-gray-100 pb-1">Keterangan Histori Penggantian</h3>
            <table class="w-full text-left text-xs border-collapse border border-gray-200">
                <thead class="bg-gray-100 font-semibold text-gray-700 text-[11px]">
                    <tr>
                        <th class="border border-gray-200 px-2 py-1.5 w-8 text-center">No</th>
                        <th class="border border-gray-200 px-2 py-1.5">Kode / Jenis</th>
                        <th class="border border-gray-200 px-2 py-1.5">Tanggal</th>
                        <th class="border border-gray-200 px-2 py-1.5">Teknisi / Catatan</th>
                    </tr>
                </thead>
                <tbody id="printHistoryTableBody" class="divide-y divide-gray-200 text-[11px]">
                    <!-- Dynamic history rows loaded when print is triggered -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL KALENDER INTERAKTIF DATE RANGE -->
<div id="calendarModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl overflow-hidden border border-gray-100">
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b border-gray-100 bg-slate-50 px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Filter Range Tanggal</h3>
                <p class="text-xs text-gray-500">Klik tanggal awal & tanggal akhir pada kalender</p>
            </div>
            <button id="btnCloseCalendarModal" type="button" class="rounded-lg p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-600">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Month Navigation Bar -->
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
            <button id="calPrevMonth" type="button" class="rounded-lg border border-gray-200 p-1.5 text-gray-600 hover:bg-gray-100 hover:text-teal-700">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <span id="calMonthYearTitle" class="text-sm font-bold text-gray-800">Juli 2026</span>
            <button id="calNextMonth" type="button" class="rounded-lg border border-gray-200 p-1.5 text-gray-600 hover:bg-gray-100 hover:text-teal-700">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Range Status Preview -->
        <div class="bg-teal-50 px-5 py-2.5 text-xs text-teal-800 border-b border-teal-100 flex items-center justify-between">
            <div>
                <span class="font-semibold">Range Dipilih:</span>
                <div id="calSelectedRangeLabel" class="font-bold text-teal-900 text-xs">Belum memilih (Klik tanggal)</div>
            </div>
            <button id="calClearSelection" type="button" class="text-[11px] text-teal-700 hover:underline">Reset</button>
        </div>

        <!-- Calendar Grid Container -->
        <div class="p-4">
            <!-- Days Header -->
            <div class="grid grid-cols-7 text-center text-xs font-semibold text-gray-400 mb-2">
                <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
            </div>

            <!-- Date Cells Grid -->
            <div id="calGrid" class="grid grid-cols-7 gap-1 text-center text-xs">
                <!-- Dynamic calendar cells -->
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
            <button id="btnCancelCalendarModal" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                Batal
            </button>
            <button id="btnApplyCalendarRange" type="button" class="rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 shadow-sm">
                Terapkan Range
            </button>
        </div>
    </div>
</div>

<!-- MODAL DETAIL LAMPU (CENTERED IN MIDDLE OF SCREEN) -->
<div id="lampDetailModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden border border-gray-100 animate-in fade-in zoom-in duration-200">
        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b border-gray-100 bg-slate-50 px-5 py-4">
            <div class="flex items-center gap-3">
                <span id="modalLampStatusBadge" class="h-3 w-3 rounded-full bg-green-500"></span>
                <div>
                    <h3 id="modalLampCode" class="text-base font-bold text-gray-900">L-0000</h3>
                    <p id="modalLampTypeName" class="text-xs text-gray-500">Jenis Lampu</p>
                </div>
            </div>
            <button id="btnCloseLampDetailModal" type="button" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-200 hover:text-gray-600">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Body Content -->
        <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
            <!-- Quick Summary Card -->
            <div class="grid grid-cols-2 gap-3 rounded-xl bg-gray-50 p-3 text-xs border border-gray-100">
                <div>
                    <span class="text-gray-400 font-medium">Status Lampu</span>
                    <div id="modalLampStatusText" class="font-bold text-green-700 mt-0.5">Aktif</div>
                </div>
                <div>
                    <span class="text-gray-400 font-medium">Kategori / Bentuk</span>
                    <div id="modalLampTypeSub" class="font-bold text-gray-800 mt-0.5">Bulat</div>
                </div>
            </div>

            <!-- History Table Section -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Histori & Range Penggantian</h4>
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-gray-100 text-gray-700 font-semibold text-[11px] border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Teknisi & Catatan</th>
                                <th class="px-3 py-2 text-right">Selisih Penggantian</th>
                            </tr>
                        </thead>
                        <tbody id="modalLampHistoryTbody" class="divide-y divide-gray-100">
                            <!-- Dynamic history rows generated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-5 py-3">
            <button id="modalBtnDeleteLamp" type="button" class="rounded-lg bg-red-50 border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100">
                Hapus Titik Lampu
            </button>
            <button id="modalBtnCloseFooter" type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const $ = (id) => document.getElementById(id);
    const buildingsData = @json($buildingsData);

    const state = {
        floorId: {{ $selectedFloor ? $selectedFloor->id : 'null' }},
        rooms: [],
        lamps: [],
        counts: { on: 0, off: 0, warning: 0, rusak: 0 },
        floorImage: @json($selectedFloorImage),
        zoom: 1,
        panX: 0,
        panY: 0,
        panning: false,
        panStartX: 0,
        panStartY: 0,
        moved: false,
        editMode: false,
        showFloor: true,
        showLamps: true,
        activeStatuses: ['on', 'off', 'warning', 'rusak'],
        searchText: '',
        filterStartDate: '',
        filterEndDate: '',
        draggingLamp: null,
    };

    const statuses = ['on', 'off', 'warning', 'rusak'];
    const colorMap = {
        on: '#22c55e',
        off: '#4b5563',
        warning: '#f97316',
        rusak: '#ef4444',
    };
    const labelMap = {
        on: 'Aktif',
        off: 'Mati',
        warning: 'Warning',
        rusak: 'Error',
    };
    const badgeMap = {
        on: 'bg-green-100 text-green-700',
        off: 'bg-gray-100 text-gray-700',
        warning: 'bg-orange-100 text-orange-700',
        rusak: 'bg-red-100 text-red-700',
    };

    function parseHistoryDateToMs(dateStr) {
        if (!dateStr) return null;
        let str = dateStr.toString().trim();

        if (str.includes('/')) {
            const parts = str.split('/');
            if (parts.length === 3) {
                let day = parts[0].padStart(2, '0');
                let month = parts[1].padStart(2, '0');
                let year = parts[2];
                return new Date(`${year}-${month}-${day}T00:00:00`).getTime();
            }
        } else if (str.includes('-')) {
            const parts = str.split('-');
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return new Date(`${parts[0]}-${parts[1].padStart(2, '0')}-${parts[2].padStart(2, '0')}T00:00:00`).getTime();
                } else {
                    return new Date(`${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}T00:00:00`).getTime();
                }
            }
        }

        const parsed = Date.parse(str);
        return isNaN(parsed) ? null : parsed;
    }

    function isLampReplacedInDateRange(lamp, startDateVal, endDateVal) {
        if ((!startDateVal && !endDateVal) || !lamp.history || lamp.history.length === 0) return false;

        let startMs = startDateVal ? new Date(startDateVal + 'T00:00:00').getTime() : null;
        let endMs = endDateVal ? new Date(endDateVal + 'T23:59:59').getTime() : null;

        return lamp.history.some(row => {
            if (!row.date) return false;
            let dMs = parseHistoryDateToMs(row.date);
            if (!dMs) return false;

            if (startMs && dMs < startMs) return false;
            if (endMs && dMs > endMs) return false;

            return true;
        });
    }

    // Mapping tipe lampu ke bentuk visual
    const lampTypeShapeMap = {
        'downlight': 'circle',
        'halogen': 'triangle',
        'tl': 'rectangle',
        'bulb': 'donut',
    };

    // Default shape adalah circle jika tipe tidak dikenali
    function getLampShape(lampType) {
        if (!lampType) return 'circle';
        if (lampType.shape === 'panjang') return 'rectangle';
        if (lampType.shape === 'bulat') return 'circle';
        if (lampType.type && lampType.type.toLowerCase().includes('tl')) return 'rectangle';
        return lampTypeShapeMap[lampType.type?.toLowerCase()] || 'circle';
    }

    // Fungsi untuk membuat elemen SVG berdasarkan bentuk
    function createLampElement(lamp, colorCode) {
        try {
            const shape = getLampShape(lamp.lamp_type);
            let svgContent = '';
            const rotation = lamp.rotation || 0;

            switch(shape) {
                case 'rectangle': // Model Panjang (TL / Tube / Batang) dengan rotasi & ukuran
                    const w = lamp.width || 32;
                    const h = lamp.height || 14;
                    svgContent = `<svg viewBox="0 0 ${w} ${h}" width="${w}" height="${h}" style="overflow: visible; transform: rotate(${rotation}deg);">
                        <rect x="2" y="2" width="${Math.max(4, w - 4)}" height="${Math.max(4, h - 4)}" rx="3" fill="${colorCode}" stroke="white" stroke-width="2"/>
                    </svg>`;
                    break;

                case 'triangle': // Halogen - segitiga
                    svgContent = `<svg viewBox="0 0 20 20" width="20" height="20" style="overflow: visible; transform: rotate(${rotation}deg);">
                        <polygon points="10,2 18,16 2,16" fill="${colorCode}" stroke="white" stroke-width="2"/>
                    </svg>`;
                    break;

                case 'donut': // Donut dengan lubang di tengah
                    svgContent = `<svg viewBox="0 0 20 20" width="20" height="20" style="overflow: visible;">
                        <circle cx="10" cy="10" r="8" fill="${colorCode}" stroke="white" stroke-width="2"/>
                        <circle cx="10" cy="10" r="4" fill="#f8fafc" stroke="white" stroke-width="0.5"/>
                    </svg>`;
                    break;

                case 'circle': // Model Bulat (Downlight / Bulb / Panel)
                default:
                    svgContent = `<svg viewBox="0 0 20 20" width="20" height="20" style="overflow: visible;">
                        <circle cx="10" cy="10" r="8" fill="${colorCode}" stroke="white" stroke-width="2"/>
                    </svg>`;
            }

            const div = document.createElement('div');
            div.innerHTML = svgContent;
            return div.firstChild;
        } catch (error) {
            console.error('Error creating lamp element:', error);
            const div = document.createElement('div');
            div.style.width = '16px';
            div.style.height = '16px';
            div.style.borderRadius = '50%';
            div.style.background = colorCode;
            div.style.boxShadow = '0 0 0 2px white';
            return div;
        }
    }

    function applyTransform() {
        $('fpInner').style.transform = `translate(${state.panX}px, ${state.panY}px) scale(${state.zoom})`;
        $('zoomBadge').textContent = Math.round(state.zoom * 100) + '%';
    }

    function pointToPercent(clientX, clientY) {
        const rect = $('fpCanvas').getBoundingClientRect();
        const x = (clientX - rect.left - state.panX) / state.zoom;
        const y = (clientY - rect.top - state.panY) / state.zoom;
        return {
            x: Math.max(0, Math.min(100, (x / rect.width) * 100)),
            y: Math.max(0, Math.min(100, (y / rect.height) * 100)),
        };
    }

    function positionPopup(el, clientX, clientY, width, height) {
        const rect = $('fpCanvas').getBoundingClientRect();
        let left = clientX - rect.left + 12;
        let top = clientY - rect.top + 12;
        if (left + width > rect.width) left = clientX - rect.left - width - 12;
        if (top + height > rect.height) top = clientY - rect.top - height - 12;
        el.style.left = Math.max(8, left) + 'px';
        el.style.top = Math.max(8, top) + 'px';
    }

    function fallbackSvg() {
        return `
            <svg viewBox="0 0 1200 760" preserveAspectRatio="none" class="h-full w-full">
                <rect width="1200" height="760" fill="#f8fafc"/>
                <rect x="80" y="90" width="1040" height="570" rx="10" fill="#ffffff" stroke="#cbd5e1" stroke-width="8"/>
                <path d="M130 145H1070M130 615H1070M130 145V615M1070 145V615" stroke="#e2e8f0" stroke-width="4" stroke-dasharray="18 18"/>
                <path d="M170 190H1030M170 570H1030" stroke="#eef2f7" stroke-width="3"/>
                <path d="M220 230V530M980 230V530" stroke="#eef2f7" stroke-width="3"/>
                <text x="95" y="720" fill="#64748b" font-size="26" font-family="Inter, Arial">Floor plan canvas</text>
            </svg>`;
    }

    function adjustCanvasAspectHeight() {
        const canvasEl = $('fpCanvas');
        const img = $('fpFloorLayer')?.querySelector('img');
        if (!canvasEl || !img) return;

        if (img.naturalWidth && img.naturalHeight) {
            const containerWidth = canvasEl.clientWidth;
            if (containerWidth > 0) {
                const targetHeight = Math.round((containerWidth * img.naturalHeight) / img.naturalWidth);
                canvasEl.style.height = targetHeight + 'px';
            }
        }
    }

    window.addEventListener('resize', adjustCanvasAspectHeight);

    if (window.ResizeObserver && $('fpCanvas')) {
        const ro = new ResizeObserver(() => {
            adjustCanvasAspectHeight();
        });
        ro.observe($('fpCanvas'));
    }

    function setFloorImage(url) {
        state.floorImage = url || '';
        const layer = $('fpFloorLayer');
        layer.innerHTML = '';
        const canvasEl = $('fpCanvas');

        if (state.floorImage) {
            const img = document.createElement('img');
            img.src = state.floorImage;
            img.alt = 'Denah lantai';
            img.className = 'h-full w-full pointer-events-none select-none p-0';
            img.draggable = false;
            img.onload = adjustCanvasAspectHeight;
            layer.appendChild(img);
            if (img.complete) adjustCanvasAspectHeight();
        } else {
            layer.innerHTML = fallbackSvg();
            if (canvasEl) {
                canvasEl.style.height = '600px';
            }
        }
        layer.style.display = state.showFloor ? '' : 'none';
    }

    function updateStats() {
        statuses.forEach((status) => {
            const el = $('cnt-' + status);
            if (el) el.textContent = state.counts[status] || 0;
        });

        const total = statuses.reduce((sum, status) => sum + (state.counts[status] || 0), 0);
        $('statTotal').textContent = total;
        $('statAktif').textContent = state.counts.on || 0;
        $('statMasalah').textContent = (state.counts.warning || 0) + (state.counts.rusak || 0);
    }

    function matchesSearchLamp(lamp) {
        const q = state.searchText.trim().toLowerCase();
        if (!q) return true;
        return [
            lamp.code,
            lamp.lamp_type?.name,
            lamp.lamp_type?.type,
        ].some((value) => (value || '').toLowerCase().includes(q));
    }

    function renderDots() {
        const layer = $('fpDotsLayer');
        layer.innerHTML = '';
        layer.style.display = state.showLamps ? '' : 'none';
        if (!state.showLamps) return;

        let filteredCount = 0;
        const isDateFiltered = Boolean(state.filterStartDate || state.filterEndDate);

        state.lamps.forEach((lamp) => {
            if (!state.activeStatuses.includes(lamp.status)) return;
            if (!matchesSearchLamp(lamp)) return;

            const isReplaced = isDateFiltered ? isLampReplacedInDateRange(lamp, state.filterStartDate, state.filterEndDate) : false;
            if (isDateFiltered && isReplaced) {
                filteredCount++;
            }

            const wrap = document.createElement('button');
            wrap.type = 'button';
            wrap.className = 'absolute flex items-center justify-center';
            wrap.style.width = '24px';
            wrap.style.height = '24px';
            wrap.style.left = lamp.position_x + '%';
            wrap.style.top = lamp.position_y + '%';
            wrap.style.transform = 'translate(-50%, -50%)';
            wrap.style.cursor = state.editMode ? 'grab' : 'pointer';
            wrap.style.background = 'transparent';
            wrap.style.border = 'none';
            wrap.style.padding = '0';
            wrap.dataset.id = lamp.id;
            wrap.title = `${lamp.code} - ${labelMap[lamp.status] || lamp.status}`;

            if (isDateFiltered) {
                if (isReplaced) {
                    wrap.classList.add('z-20');
                    wrap.style.opacity = '1';
                } else {
                    wrap.style.opacity = '0.12';
                }
            } else {
                wrap.style.opacity = '1';
                if (lamp.status === 'warning' || lamp.status === 'rusak') {
                    const ring = document.createElement('span');
                    ring.className = 'absolute inset-0 rounded-full';
                    ring.style.background = colorMap[lamp.status];
                    ring.style.opacity = '.55';
                    ring.style.animation = 'fpPing 1.5s cubic-bezier(0,0,.2,1) infinite';
                    wrap.appendChild(ring);
                }
            }

            const lampElement = createLampElement(lamp, colorMap[lamp.status] || '#64748b');
            lampElement.style.filter = 'drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15))';
            wrap.appendChild(lampElement);

            // Overlay kode lampu di dalam titik (seperti pada maintenance)
            const codeLabel = document.createElement('span');
            codeLabel.className = 'absolute z-10 text-[8px] font-black text-white pointer-events-none select-none tracking-tighter text-center leading-none';
            codeLabel.style.textShadow = '0 1px 2px rgba(0,0,0,0.95), 0 0 3px rgba(0,0,0,0.95)';
            const displayCode = lamp.code ? lamp.code.replace(/^L-/, '') : '';
            codeLabel.textContent = displayCode;
            wrap.appendChild(codeLabel);

            wrap.addEventListener('mousedown', (event) => {
                if (!state.editMode || event.button !== 0) return;
                event.stopPropagation();
                state.draggingLamp = lamp;
                state.moved = false;
                wrap.style.cursor = 'grabbing';
            });

            wrap.addEventListener('click', (event) => {
                event.stopPropagation();
                if (state.moved) return;
                openLampDetailModal(lamp);
            });

            layer.appendChild(wrap);
        });

        // Update result badge for date filter
        const badge = $('filterResultBadge');
        if (badge) {
            if (isDateFiltered) {
                badge.classList.remove('hidden');
                $('filteredLampCount').textContent = filteredCount;
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    function renderAll() {
        renderDots();
    }

    function showTooltip(lamp, clientX, clientY) {
        $('ttCode').textContent = lamp.code || '-';
        $('ttName').textContent = lamp.lamp_type ? lamp.lamp_type.name : '-';
        $('ttWatt').textContent = lamp.lamp_type ? `${lamp.lamp_type.type}` : '';
        if ($('ttStatusSelect')) $('ttStatusSelect').value = lamp.status;

        const badge = $('ttBadge');
        badge.textContent = labelMap[lamp.status] || lamp.status;
        badge.className = 'inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ' + (badgeMap[lamp.status] || 'bg-gray-100 text-gray-700');

        // Populate history rows
        const historyContainer = $('ttHistoryList');
        if (historyContainer) {
            historyContainer.innerHTML = '';
            if (lamp.history && lamp.history.length > 0) {
                lamp.history.forEach(row => {
                    const el = document.createElement('div');
                    el.className = 'border-b border-gray-100 pb-1 last:border-b-0 last:pb-0 mb-1';
                    el.innerHTML = `
                        <div class="flex justify-between font-semibold">
                            <span>${row.date}</span>
                            <span class="text-teal-700 font-medium">${row.technician}</span>
                        </div>
                        <div class="text-[11px] text-gray-500">${row.notes}</div>
                    `;
                    historyContainer.appendChild(el);
                });
            } else {
                historyContainer.innerHTML = '<div class="text-gray-400 italic py-1 text-center">Belum ada riwayat penggantian.</div>';
            }
        }

        // Tampilkan kontrol rotasi untuk lampu model panjang / TL
        const rotationControl = $('ttRotationControl');
        const shape = getLampShape(lamp.lamp_type);
        const isRotatable = shape === 'rectangle' || shape === 'triangle' || (lamp.lamp_type && lamp.lamp_type.shape === 'panjang');
        if (isRotatable) {
            rotationControl.classList.remove('hidden');
            $('ttRotation').value = lamp.rotation || 0;
            $('ttWidth').value = lamp.width || 32;
            $('ttHeight').value = lamp.height || 14;
        } else {
            rotationControl.classList.add('hidden');
        }

        const tooltip = $('fpTooltip');
        tooltip.dataset.lampId = lamp.id;
        tooltip.classList.remove('hidden');
        positionPopup(tooltip, clientX, clientY, 288, 380); // Adjusted height to accommodate history
        window._fpActiveLamp = lamp;
    }

    function hideTooltip() {
        $('fpTooltip').classList.add('hidden');
        window._fpActiveLamp = null;
    }

    function hideAddModal() {
        $('fpAddModal').classList.add('hidden');
    }

    async function loadFloorData(floorId) {
        if (!floorId) return;
        state.floorId = floorId;
        hideTooltip();
        hideAddModal();

        const res = await fetch('/floor-plan/data?floor_id=' + encodeURIComponent(floorId), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        if (!res.ok) throw new Error('Gagal memuat data lantai.');

        const data = await res.json();
        state.rooms = data.rooms || [];
        state.lamps = state.rooms.flatMap((room) => room.lamps || []);
        state.counts = Object.assign({ on: 0, off: 0, warning: 0, rusak: 0 }, data.lamp_counts || {});
        updateStats();
        setFloorImage(data.floor?.floor_plan_image || '');
        renderDots();
    }

    async function saveLampPosition(lamp) {
        const res = await fetch('/floor-plan/lamp/' + lamp.id + '/position', {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({
                position_x: lamp.position_x,
                position_y: lamp.position_y,
            }),
        });
        if (!res.ok) {
            alert('Gagal menyimpan posisi titik lampu.');
            await loadFloorData(state.floorId);
        }
    }

    function resetView() {
        state.zoom = 1;
        state.panX = 0;
        state.panY = 0;
        applyTransform();
    }

    if ($('btnTogglePanel')) {
        $('btnTogglePanel').addEventListener('click', () => {
            $('fpPanel')?.classList.toggle('hidden');
        });
    }

    if ($('btnEditMode')) {
        $('btnEditMode').addEventListener('click', function () {
            state.editMode = !state.editMode;
            hideTooltip();
            hideAddModal();
            if ($('editBadge')) $('editBadge').classList.toggle('hidden', !state.editMode);
            if ($('editModeLabel')) $('editModeLabel').textContent = state.editMode ? 'Mode Edit Aktif' : '+ Edit Titik Lampu';
            if (state.editMode) {
                this.className = 'rounded-lg border border-teal-700 bg-teal-700 px-3 py-2 text-sm font-semibold text-white hover:bg-teal-800 shadow-sm';
            } else {
                this.className = 'rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50';
            }
            if ($('fpCanvas')) $('fpCanvas').style.cursor = state.editMode ? 'crosshair' : 'grab';
            renderDots();
        });
    }

    document.querySelectorAll('.fp-status-filter').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            state.activeStatuses = Array.from(document.querySelectorAll('.fp-status-filter:checked')).map((item) => item.value);
            renderDots();
        });
    });

    if ($('fpSearch')) {
        $('fpSearch').addEventListener('input', function () {
            state.searchText = this.value;
            renderDots();
        });
    }

    if ($('layerLamps')) {
        $('layerLamps').addEventListener('change', function () {
            state.showLamps = this.checked;
            renderDots();
        });
    }

    if ($('layerFloor')) {
        $('layerFloor').addEventListener('change', function () {
            state.showFloor = this.checked;
            if ($('fpFloorLayer')) $('fpFloorLayer').style.display = this.checked ? '' : 'none';
        });
    }

    if ($('btnZoomIn')) {
        $('btnZoomIn').addEventListener('click', () => {
            state.zoom = Math.min(2.4, state.zoom + 0.1);
            applyTransform();
        });
    }

    if ($('btnZoomOut')) {
        $('btnZoomOut').addEventListener('click', () => {
            state.zoom = Math.max(0.65, state.zoom - 0.1);
            applyTransform();
        });
    }

    if ($('btnResetView')) $('btnResetView').addEventListener('click', resetView);
    if ($('ttClose')) $('ttClose').addEventListener('click', hideTooltip);
    if ($('addModalClose')) $('addModalClose').addEventListener('click', hideAddModal);

    ['fpTooltip', 'fpAddModal'].forEach((id) => {
        const el = $(id);
        if (el) {
            ['click', 'mousedown', 'wheel'].forEach((eventName) => {
                el.addEventListener(eventName, (event) => event.stopPropagation());
            });
        }
    });

    if ($('ttSaveStatus')) {
        $('ttSaveStatus').addEventListener('click', async function () {
            const lamp = window._fpActiveLamp;
            if (!lamp) return;
            const status = $('ttStatusSelect').value;
            if (status === lamp.status) return;

            this.disabled = true;
            const res = await fetch('/floor-plan/lamp/' + lamp.id + '/status', {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({ status }),
            });
            this.disabled = false;

            if (!res.ok) {
                alert('Gagal menyimpan status.');
                return;
            }

            state.counts[lamp.status] = Math.max(0, (state.counts[lamp.status] || 0) - 1);
            state.counts[status] = (state.counts[status] || 0) + 1;
            lamp.status = status;
            updateStats();
            renderDots();
        });
    }

    if ($('ttDelete')) {
        $('ttDelete').addEventListener('click', async function () {
            const lamp = window._fpActiveLamp;
            if (!lamp) return;
            if (!confirm('Hapus titik lampu "' + lamp.code + '"?')) return;

            const res = await fetch('/floor-plan/lamp/' + lamp.id, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            if (!res.ok) {
                alert('Gagal menghapus titik lampu.');
                return;
            }

            state.lamps = state.lamps.filter((item) => item.id !== lamp.id);
            state.counts[lamp.status] = Math.max(0, (state.counts[lamp.status] || 0) - 1);
            updateStats();
            hideTooltip();
            renderDots();
        });
    }

    if ($('ttRotateLeft')) {
        $('ttRotateLeft').addEventListener('click', () => {
            if ($('ttRotation')) {
                let rotation = parseInt($('ttRotation').value) || 0;
                rotation = (rotation - 90 + 360) % 360;
                $('ttRotation').value = rotation;
            }
        });
    }

    if ($('ttRotateRight')) {
        $('ttRotateRight').addEventListener('click', () => {
            if ($('ttRotation')) {
                let rotation = parseInt($('ttRotation').value) || 0;
                rotation = (rotation + 90) % 360;
                $('ttRotation').value = rotation;
            }
        });
    }

    if ($('ttSaveRotation')) {
        $('ttSaveRotation').addEventListener('click', async function () {
            const lamp = window._fpActiveLamp;
            if (!lamp) return;
            const rotation = parseInt($('ttRotation').value) || 0;
            if (rotation === (lamp.rotation || 0)) return;

            this.disabled = true;
            const res = await fetch('/floor-plan/lamp/' + lamp.id + '/rotation', {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({ rotation }),
            });
            this.disabled = false;

            if (!res.ok) {
                alert('Gagal menyimpan rotasi.');
                return;
            }

            lamp.rotation = rotation;
            renderDots();
        });
    }

    $('ttSaveDimensions')?.addEventListener('click', async function () {
        const lamp = window._fpActiveLamp;
        if (!lamp) return;
        const width = parseInt($('ttWidth').value) || 32;
        const height = parseInt($('ttHeight').value) || 14;

        this.disabled = true;
        const res = await fetch('/floor-plan/lamp/' + lamp.id + '/dimensions', {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ width, height }),
        });
        this.disabled = false;

        if (!res.ok) {
            alert('Gagal menyimpan ukuran lampu.');
            return;
        }

        lamp.width = width;
        lamp.height = height;
        renderDots();
        showTooltip(lamp, parseFloat($('fpTooltip').style.left) + $('fpCanvas').getBoundingClientRect().left, parseFloat($('fpTooltip').style.top) + $('fpCanvas').getBoundingClientRect().top);
    });

    $('addSaveBtn').addEventListener('click', async function () {
        const lampTypeId = $('addLampType').value;
        if (!lampTypeId) {
            alert('Pilih jenis lampu terlebih dahulu.');
            return;
        }

        this.disabled = true;
        const res = await fetch('/floor-plan/lamp', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({
                floor_id: state.floorId,
                lamp_type_id: lampTypeId,
                position_x: parseFloat($('addPosX').value),
                position_y: parseFloat($('addPosY').value),
            }),
        });
        this.disabled = false;

        if (!res.ok) {
            const error = await res.json().catch(() => ({}));
            alert(error.message || 'Gagal menyimpan titik lampu.');
            return;
        }

        const lamp = await res.json();
        state.lamps.push(lamp);
        state.counts[lamp.status] = (state.counts[lamp.status] || 0) + 1;
        updateStats();
        hideAddModal();
        renderDots();
    });

    if ($('uploadInput')) {
        $('uploadInput').addEventListener('change', async function (event) {
            const file = event.target.files[0];
            event.target.value = '';
            if (!file || !state.floorId) return;

            const form = new FormData();
            form.append('image', file);
            const res = await fetch('/floor-plan/' + state.floorId + '/upload', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: form,
            });

            if (!res.ok) {
                alert('Gagal upload denah.');
                return;
            }

            const data = await res.json();
            setFloorImage(data.url ? data.url + '?t=' + Date.now() : '');
        });
    }

    if ($('fpCanvas')) {
        $('fpCanvas').addEventListener('mousedown', function (event) {
            if (event.button !== 0 || state.editMode) return;
            state.panning = true;
            state.moved = false;
            state.panStartX = event.clientX - state.panX;
            state.panStartY = event.clientY - state.panY;
            this.style.cursor = 'grabbing';
            event.preventDefault();
        });
    }

    window.addEventListener('mousemove', function (event) {
        if (state.draggingLamp) {
            const point = pointToPercent(event.clientX, event.clientY);
            state.draggingLamp.position_x = point.x.toFixed(1);
            state.draggingLamp.position_y = point.y.toFixed(1);
            state.moved = true;
            renderDots();
            return;
        }

        if (!state.panning) return;
        state.panX = event.clientX - state.panStartX;
        state.panY = event.clientY - state.panStartY;
        state.moved = true;
        applyTransform();
    });

    window.addEventListener('mouseup', async function () {
        if (state.draggingLamp) {
            const lamp = state.draggingLamp;
            state.draggingLamp = null;
            await saveLampPosition(lamp);
            renderDots();
        }

        state.panning = false;
        if ($('fpCanvas')) $('fpCanvas').style.cursor = state.editMode ? 'crosshair' : 'grab';
    });

    if ($('fpCanvas')) {
        $('fpCanvas').addEventListener('wheel', function (event) {
            event.preventDefault();
            state.zoom = Math.max(0.65, Math.min(2.4, state.zoom + (event.deltaY > 0 ? -0.04 : 0.04)));
            applyTransform();
        }, { passive: false });
    }

    if ($('fpCanvas')) {
        $('fpCanvas').addEventListener('click', function (event) {
            if (state.moved) {
                state.moved = false;
                return;
            }
            if (!state.editMode) {
                hideTooltip();
                return;
            }
            if (event.target.closest('#fpTooltip') || event.target.closest('#fpAddModal')) return;

            const point = pointToPercent(event.clientX, event.clientY);
            if ($('addPosX')) $('addPosX').value = point.x.toFixed(1);
            if ($('addPosY')) $('addPosY').value = point.y.toFixed(1);
            if ($('addLampType')) $('addLampType').value = '';

            const modal = $('fpAddModal');
            if (modal) {
                modal.classList.remove('hidden');
                positionPopup(modal, event.clientX, event.clientY, 288, 230);
            }
        });
    }

    if ($('buildingSelect')) {
        $('buildingSelect').addEventListener('change', function () {
            const building = buildingsData.find((item) => item.id == this.value);
            const floorSelect = $('floorSelect');
            if (floorSelect) {
                floorSelect.innerHTML = '';
                (building?.floors || []).forEach((floor) => {
                    const option = document.createElement('option');
                    option.value = floor.id;
                    option.textContent = floor.name;
                    floorSelect.appendChild(option);
                });
            }

            if (building?.floors?.length) {
                loadFloorData(building.floors[0].id).catch((error) => {
                    console.error(error);
                    alert('Gagal memuat floor plan.');
                });
            }
        });
    }

    if ($('floorSelect')) {
        $('floorSelect').addEventListener('change', function () {
            loadFloorData(this.value).catch((error) => {
                console.error(error);
                alert('Gagal memuat floor plan.');
            });
        });
    }

    // Event listeners Date Filter
    if ($('btnApplyDateFilter')) {
        $('btnApplyDateFilter').addEventListener('click', function () {
            state.filterStartDate = $('filterStartDate').value;
            state.filterEndDate = $('filterEndDate').value;
            renderDots();
        });
    }

    if ($('btnResetDateFilter')) {
        $('btnResetDateFilter').addEventListener('click', function () {
            $('filterStartDate').value = '';
            $('filterEndDate').value = '';
            state.filterStartDate = '';
            state.filterEndDate = '';
            renderDots();
        });
    }

    // Export PDF Trigger
    function triggerExportPdf() {
        const isDateFiltered = Boolean(state.filterStartDate || state.filterEndDate);
        const buildingSelect = $('buildingSelect');
        const floorSelect = $('floorSelect');
        if ($('printBuildingName')) $('printBuildingName').textContent = buildingSelect ? buildingSelect.options[buildingSelect.selectedIndex]?.text : '-';
        if ($('printFloorName')) $('printFloorName').textContent = floorSelect ? floorSelect.options[floorSelect.selectedIndex]?.text : '-';
        
        let dateRangeLabel = 'Semua Tanggal';
        if (state.filterStartDate && state.filterEndDate) {
            dateRangeLabel = `${state.filterStartDate} s/d ${state.filterEndDate}`;
        } else if (state.filterStartDate) {
            dateRangeLabel = `Mulai ${state.filterStartDate}`;
        } else if (state.filterEndDate) {
            dateRangeLabel = `Sampai ${state.filterEndDate}`;
        }
        if ($('printFilterDateLabel')) $('printFilterDateLabel').textContent = dateRangeLabel;

        const tbody = $('printHistoryTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            let historyRows = [];

            state.lamps.forEach(lamp => {
                if (!lamp.history || lamp.history.length === 0) return;

                lamp.history.forEach(h => {
                    const matches = isDateFiltered ? isLampReplacedInDateRange(lamp, state.filterStartDate, state.filterEndDate) : true;
                    if (matches) {
                        historyRows.push({
                            lamp_code: lamp.code,
                            lamp_type: lamp.lamp_type?.name || 'Lampu',
                            date: h.date,
                            technician: h.technician || '-',
                            notes: h.notes || 'Penggantian lampu',
                        });
                    }
                });
            });

            if (historyRows.length > 0) {
                historyRows.forEach((row, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="border border-gray-200 px-2 py-1 text-center font-medium text-gray-500">${idx + 1}</td>
                        <td class="border border-gray-200 px-2 py-1 font-bold text-teal-800">
                            ${row.lamp_code}
                            <div class="text-[10px] font-normal text-gray-500">${row.lamp_type}</div>
                        </td>
                        <td class="border border-gray-200 px-2 py-1 font-medium text-gray-700 whitespace-nowrap">${row.date}</td>
                        <td class="border border-gray-200 px-2 py-1 text-gray-600">
                            <span class="font-semibold text-gray-800">${row.technician}</span>
                            <div class="text-[10px] text-gray-500">${row.notes}</div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="border border-gray-200 px-3 py-6 text-center text-gray-400 italic">
                            Tidak ada riwayat penggantian lampu ${isDateFiltered ? 'pada rentang tanggal tersebut' : 'tercatat'}.
                        </td>
                    </tr>
                `;
            }
        }

        const printCanvasContainer = $('printCanvasContainer');
        if (printCanvasContainer) {
            printCanvasContainer.innerHTML = '';

            const printWrap = document.createElement('div');
            printWrap.className = 'relative w-full h-[400px] overflow-hidden bg-slate-50 rounded border border-slate-200';

            // 1. Floor Plan Image / SVG
            if (state.floorImage) {
                const img = document.createElement('img');
                img.src = state.floorImage;
                img.className = 'w-full h-full object-contain pointer-events-none';
                printWrap.appendChild(img);
            } else {
                const svgDiv = document.createElement('div');
                svgDiv.className = 'w-full h-full';
                svgDiv.innerHTML = fallbackSvg();
                printWrap.appendChild(svgDiv);
            }

            // 2. Lamp Dots Layer using DIVs (bypasses button print hiding)
            const dotsLayer = document.createElement('div');
            dotsLayer.className = 'absolute inset-0';

            state.lamps.forEach(lamp => {
                if (!state.activeStatuses.includes(lamp.status)) return;

                const isReplaced = isDateFiltered ? isLampReplacedInDateRange(lamp, state.filterStartDate, state.filterEndDate) : false;

                const dot = document.createElement('div');
                dot.className = 'absolute flex items-center justify-center';
                dot.style.width = '24px';
                dot.style.height = '24px';
                dot.style.left = lamp.position_x + '%';
                dot.style.top = lamp.position_y + '%';
                dot.style.transform = 'translate(-50%, -50%)';

                if (isDateFiltered) {
                    if (isReplaced) {
                        dot.style.zIndex = '20';
                        dot.style.opacity = '1';
                    } else {
                        dot.style.opacity = '0.12';
                    }
                } else {
                    dot.style.opacity = '1';
                    if (lamp.status === 'warning' || lamp.status === 'rusak') {
                        const ring = document.createElement('span');
                        ring.className = 'absolute inset-0 rounded-full';
                        ring.style.background = colorMap[lamp.status];
                        ring.style.opacity = '.55';
                        dot.appendChild(ring);
                    }
                }

                const lampEl = createLampElement(lamp, colorMap[lamp.status] || '#64748b');
                dot.appendChild(lampEl);

                // Overlay kode lampu di dalam titik untuk PDF cetak
                const codeLabel = document.createElement('span');
                codeLabel.className = 'absolute z-10 text-[8px] font-black text-white pointer-events-none select-none tracking-tighter text-center leading-none';
                codeLabel.style.textShadow = '0 1px 2px rgba(0,0,0,0.95), 0 0 3px rgba(0,0,0,0.95)';
                const displayCode = lamp.code ? lamp.code.replace(/^L-/, '') : '';
                codeLabel.textContent = displayCode;
                dot.appendChild(codeLabel);

                dotsLayer.appendChild(dot);
            });

            printWrap.appendChild(dotsLayer);
            printCanvasContainer.appendChild(printWrap);
        }

        window.print();
    }

    if ($('btnExportPdf')) {
        $('btnExportPdf').addEventListener('click', triggerExportPdf);
    }

    // ── CALENDAR RANGE PICKER MODAL LOGIC ──────────────────────────────
    const monthNamesIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    let calState = {
        year: new Date().getFullYear(),
        month: new Date().getMonth(),
        startDate: null, // 'YYYY-MM-DD'
        endDate: null,   // 'YYYY-MM-DD'
    };

    function formatYmd(year, month, day) {
        const m = (month + 1).toString().padStart(2, '0');
        const d = day.toString().padStart(2, '0');
        return `${year}-${m}-${d}`;
    }

    function updateCalendarRangeLabel() {
        const lbl = $('calSelectedRangeLabel');
        if (!lbl) return;

        if (calState.startDate && calState.endDate) {
            lbl.textContent = `${calState.startDate} s/d ${calState.endDate}`;
        } else if (calState.startDate) {
            lbl.textContent = `${calState.startDate} (Klik tanggal akhir...)`;
        } else {
            lbl.textContent = 'Belum memilih (Klik tanggal awal)';
        }
    }

    function renderCalendarGrid() {
        const grid = $('calGrid');
        if (!grid) return;
        grid.innerHTML = '';

        if ($('calMonthYearTitle')) {
            $('calMonthYearTitle').textContent = `${monthNamesIndo[calState.month]} ${calState.year}`;
        }

        const firstDayIndex = new Date(calState.year, calState.month, 1).getDay();
        const totalDays = new Date(calState.year, calState.month + 1, 0).getDate();

        // Empty cells for alignment
        for (let i = 0; i < firstDayIndex; i++) {
            const empty = document.createElement('div');
            empty.className = 'h-9 w-full';
            grid.appendChild(empty);
        }

        for (let d = 1; d <= totalDays; d++) {
            const ymd = formatYmd(calState.year, calState.month, d);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'h-9 w-full text-xs font-semibold transition-all duration-150 flex items-center justify-center select-none rounded-lg';

            const isStart = calState.startDate === ymd;
            const isEnd = calState.endDate === ymd;
            const isInRange = calState.startDate && calState.endDate && ymd > calState.startDate && ymd < calState.endDate;

            if (isStart && isEnd) {
                btn.className += ' bg-teal-700 text-white font-bold ring-2 ring-teal-500 shadow-md';
            } else if (isStart) {
                btn.className += ' bg-teal-700 text-white font-bold rounded-r-none ring-2 ring-teal-500 shadow-md';
            } else if (isEnd) {
                btn.className += ' bg-teal-700 text-white font-bold rounded-l-none ring-2 ring-teal-500 shadow-md';
            } else if (isInRange) {
                btn.className += ' bg-teal-100 text-teal-900 rounded-none font-bold';
            } else {
                btn.className += ' hover:bg-teal-50 text-gray-700';
            }

            btn.textContent = d;

            btn.addEventListener('click', function () {
                if (!calState.startDate || (calState.startDate && calState.endDate)) {
                    calState.startDate = ymd;
                    calState.endDate = null;
                } else if (ymd < calState.startDate) {
                    calState.startDate = ymd;
                    calState.endDate = null;
                } else {
                    calState.endDate = ymd;
                }
                renderCalendarGrid();
            });

            grid.appendChild(btn);
        }

        updateCalendarRangeLabel();
    }

    // Modal controls
    const calModal = $('calendarModal');
    if ($('btnOpenCalendarModal')) {
        $('btnOpenCalendarModal').addEventListener('click', function () {
            calModal.classList.remove('hidden');
            calModal.classList.add('flex');
            renderCalendarGrid();
        });
    }

    function closeCalendarModal() {
        if (calModal) {
            calModal.classList.add('hidden');
            calModal.classList.remove('flex');
        }
    }

    if ($('btnCloseCalendarModal')) $('btnCloseCalendarModal').addEventListener('click', closeCalendarModal);
    if ($('btnCancelCalendarModal')) $('btnCancelCalendarModal').addEventListener('click', closeCalendarModal);

    if ($('calPrevMonth')) {
        $('calPrevMonth').addEventListener('click', function () {
            calState.month--;
            if (calState.month < 0) {
                calState.month = 11;
                calState.year--;
            }
            renderCalendarGrid();
        });
    }

    if ($('calNextMonth')) {
        $('calNextMonth').addEventListener('click', function () {
            calState.month++;
            if (calState.month > 11) {
                calState.month = 0;
                calState.year++;
            }
            renderCalendarGrid();
        });
    }

    if ($('calClearSelection')) {
        $('calClearSelection').addEventListener('click', function () {
            calState.startDate = null;
            calState.endDate = null;
            renderCalendarGrid();
        });
    }

    if ($('btnApplyCalendarRange')) {
        $('btnApplyCalendarRange').addEventListener('click', function () {
            state.filterStartDate = calState.startDate || '';
            state.filterEndDate = calState.endDate || '';

            const triggerLabel = $('calendarTriggerLabel');
            if (triggerLabel) {
                if (state.filterStartDate && state.filterEndDate) {
                    triggerLabel.textContent = `Range: ${state.filterStartDate} s/d ${state.filterEndDate}`;
                    triggerLabel.className = 'font-bold text-teal-800';
                } else if (state.filterStartDate) {
                    triggerLabel.textContent = `Mulai: ${state.filterStartDate}`;
                    triggerLabel.className = 'font-bold text-teal-800';
                } else {
                    triggerLabel.textContent = 'Pilih Range Tanggal (Kalender)...';
                    triggerLabel.className = 'font-medium text-gray-600';
                }
            }

            closeCalendarModal();
            renderDots();
        });
    }

    if ($('btnResetDateFilter')) {
        $('btnResetDateFilter').addEventListener('click', function () {
            calState.startDate = null;
            calState.endDate = null;
            state.filterStartDate = '';
            state.filterEndDate = '';

            const triggerLabel = $('calendarTriggerLabel');
            if (triggerLabel) {
                triggerLabel.textContent = 'Pilih Range Tanggal (Kalender)...';
                triggerLabel.className = 'font-medium text-gray-600';
            }

            renderDots();
        });
    }

    // ── CENTERED LAMP DETAIL MODAL LOGIC ──────────────────────────────
    function calculateDateIntervalLabel(prevDateStr, currDateStr) {
        if (!prevDateStr) return '<span class="text-gray-400 font-normal">-</span>';
        const prevMs = parseHistoryDateToMs(prevDateStr);
        const currMs = parseHistoryDateToMs(currDateStr);

        if (!prevMs || !currMs) return '-';

        const diffMs = currMs - prevMs;
        if (diffMs < 0) return '-';

        const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        if (days === 0) return '<span class="font-bold text-teal-700">0 hari</span>';
        if (days < 30) return `<span class="font-bold text-teal-700">${days} hari</span>`;

        const months = Math.floor(days / 30);
        const remDays = days % 30;
        if (remDays === 0) return `<span class="font-bold text-teal-700">${months} bulan</span>`;
        return `<span class="font-bold text-teal-700">${months} bulan ${remDays} hari</span>`;
    }

    function openLampDetailModal(lamp) {
        state.selectedLamp = lamp;
        const modal = $('lampDetailModal');
        if (!modal) return;

        if ($('modalLampCode')) $('modalLampCode').textContent = lamp.code || 'Lampu';
        if ($('modalLampTypeName')) $('modalLampTypeName').textContent = lamp.lamp_type?.name || 'Tipe Lampu Tidak Diketahui';
        if ($('modalLampStatusText')) $('modalLampStatusText').textContent = labelMap[lamp.status] || lamp.status;
        if ($('modalLampStatusBadge')) $('modalLampStatusBadge').style.background = colorMap[lamp.status] || '#22c55e';
        if ($('modalLampTypeSub')) $('modalLampTypeSub').textContent = lamp.lamp_type?.type || 'Standard';

        const tbody = $('modalLampHistoryTbody');
        if (tbody) {
            tbody.innerHTML = '';
            const history = lamp.history ? [...lamp.history] : [];

            // Sort ascending by date for interval calculation
            history.sort((a, b) => {
                const msA = parseHistoryDateToMs(a.date) || 0;
                const msB = parseHistoryDateToMs(b.date) || 0;
                return msA - msB;
            });

            if (history.length > 0) {
                history.forEach((h, idx) => {
                    const prevDate = idx > 0 ? history[idx - 1].date : null;
                    const intervalText = calculateDateIntervalLabel(prevDate, h.date);

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 transition-colors';
                    tr.innerHTML = `
                        <td class="px-3 py-2.5 font-semibold text-gray-800 whitespace-nowrap">${h.date || '-'}</td>
                        <td class="px-3 py-2.5 text-gray-600">
                            <span class="font-bold text-gray-800">${h.technician || '-'}</span>
                            <div class="text-[10px] text-gray-500">${h.notes || 'Penggantian lampu'}</div>
                        </td>
                        <td class="px-3 py-2.5 text-right font-medium whitespace-nowrap">${intervalText}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-3 py-6 text-center text-gray-400 italic">
                            Belum ada riwayat penggantian untuk lampu ini.
                        </td>
                    </tr>
                `;
            }
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLampDetailModal() {
        const modal = $('lampDetailModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    if ($('btnCloseLampDetailModal')) $('btnCloseLampDetailModal').addEventListener('click', closeLampDetailModal);
    if ($('modalBtnCloseFooter')) $('modalBtnCloseFooter').addEventListener('click', closeLampDetailModal);
    if ($('modalBtnDeleteLamp')) {
        $('modalBtnDeleteLamp').addEventListener('click', async function () {
            if (!state.selectedLamp) return;
            if (confirm(`Apakah Anda yakin ingin menghapus titik lampu ${state.selectedLamp.code}?`)) {
                await deleteLamp(state.selectedLamp.id);
                closeLampDetailModal();
            }
        });
    }

    const style = document.createElement('style');
    style.textContent = '@keyframes fpPing{75%,100%{transform:scale(2.4);opacity:0}}';
    document.head.appendChild(style);

    setFloorImage(state.floorImage);
    applyTransform();
    if (state.floorId) {
        loadFloorData(state.floorId).catch((error) => {
            console.error(error);
            alert('Gagal memuat floor plan.');
        });
    }
});
</script>
@endpush
