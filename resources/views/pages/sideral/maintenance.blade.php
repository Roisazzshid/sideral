@extends('layouts.app')

@php
    $tabs = [
        'daftar' => 'Daftar Maintenance',
        'jadwal' => 'Jadwal Maintenance',
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb pageTitle="Maintenance" />

    <div class="space-y-5">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Maintenance belum bisa disimpan.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Header & Navigation -->
        <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Maintenance</h3>
                <p class="mt-1 text-sm text-gray-500">Sistem tiket pengerjaan, pembersihan, dan jadwal pemeliharaan fasilitas lampu.</p>
            </div>

            <button id="btnOpenMaintenanceModal" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                Buat Maintenance
            </button>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex flex-wrap gap-2 rounded-lg border border-gray-200 bg-white p-2">
            @foreach($tabs as $key => $label)
                <a href="{{ route('maintenance', array_merge(request()->except('tab'), ['tab' => $key])) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold {{ $tab === $key ? 'bg-teal-700 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Tab 1: Daftar Maintenance -->
        @if ($tab === 'daftar')
            <form method="GET" action="{{ route('maintenance') }}" class="rounded-lg border border-gray-200 bg-white px-5 py-4">
                <input type="hidden" name="tab" value="daftar">
                <div class="grid gap-3 md:grid-cols-3 xl:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Status</label>
                        <select name="status" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(($filters['status'] ?? '') == 'pending')>Baru (Pending)</option>
                            <option value="in_progress" @selected(($filters['status'] ?? '') == 'in_progress')>Proses</option>
                            <option value="completed" @selected(($filters['status'] ?? '') == 'completed')>Selesai</option>
                            <option value="cancelled" @selected(($filters['status'] ?? '') == 'cancelled')>Batal</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Prioritas</label>
                        <select name="priority" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="">Semua Prioritas</option>
                            <option value="high" @selected(($filters['priority'] ?? '') == 'high')>Tinggi (High)</option>
                            <option value="medium" @selected(($filters['priority'] ?? '') == 'medium')>Sedang (Medium)</option>
                            <option value="low" @selected(($filters['priority'] ?? '') == 'low')>Rendah (Low)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Cari</label>
                        <div class="flex gap-2">
                            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari tiket, deskripsi, teknisi..." class="h-10 min-w-0 flex-1 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <button type="submit" class="h-10 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">Filter</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h4 class="font-semibold text-gray-800">Daftar Maintenance</h4>
                    <p class="mt-1 text-sm text-gray-500">Tiket perbaikan dan pemeliharaan lampu yang terdaftar dalam sistem.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                            <tr>
                                <th class="w-16 px-5 py-3">No</th>
                                <th class="px-5 py-3">Tanggal Jadwal</th>
                                <th class="px-5 py-3">Area / Lantai</th>
                                <th class="px-5 py-3">Nomor Lampu</th>
                                <th class="px-5 py-3">Jenis Masalah</th>
                                <th class="px-5 py-3">Deskripsi</th>
                                <th class="px-5 py-3">Prioritas</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Teknisi</th>
                                <th class="w-32 px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($maintenances as $index => $mt)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4 text-gray-700">{{ $mt->scheduled_date?->format('d/m/Y') ?: '-' }}</td>
                                    <td class="px-5 py-4 text-gray-700">
                                        <div class="font-medium text-gray-800">{{ $mt->floor?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $mt->floor?->building?->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="font-semibold text-teal-700">{{ $mt->lamp?->code ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-4 font-medium text-gray-800">{{ $mt->type }}</td>
                                    <td class="px-5 py-4 text-gray-600 max-w-xs truncate" title="{{ $mt->description }}">{{ $mt->description ?: '-' }}</td>
                                    <td class="px-5 py-4">
                                        @if($mt->priority === 'high')
                                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Tinggi</span>
                                        @elseif($mt->priority === 'medium')
                                            <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/15">Sedang</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/10">Rendah</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($mt->status === 'completed')
                                            <span class="inline-flex items-center gap-1 rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                <svg class="h-3 w-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Approved
                                            </span>
                                        @elseif($mt->status === 'in_progress')
                                            <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Sudah Dikerjakan</span>
                                        @elseif($mt->status === 'cancelled')
                                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Batal</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Baru (Pending)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-gray-700">{{ $mt->assigned_to ?: '-' }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-1.5">
                                            @if($mt->status === 'pending')
                                                <!-- State 1: Baru (Pending) - Blue Edit & Yellow Process -->
                                                <button
                                                    type="button"
                                                    class="btn-edit-maintenance inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50"
                                                    title="Edit Permintaan Pekerjaan"
                                                    data-action="{{ route('maintenance.update', $mt) }}"
                                                    data-floor-id="{{ $mt->floor_id }}"
                                                    data-lamp-id="{{ $mt->lamp_id }}"
                                                    data-type="{{ $mt->type }}"
                                                    data-description="{{ $mt->description }}"
                                                    data-priority="{{ $mt->priority }}"
                                                    data-status="{{ $mt->status }}"
                                                    data-scheduled-date="{{ $mt->scheduled_date?->toDateString() }}"
                                                    data-assigned-to="{{ $mt->assigned_to }}"
                                                >
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M12 20h9"></path>
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <button
                                                    type="button"
                                                    class="btn-process-work inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100"
                                                    title="Proses Pengerjaan"
                                                    data-action="{{ route('maintenance.work', $mt) }}"
                                                    data-work-date="{{ now()->toDateString() }}"
                                                    data-assigned-to="{{ $mt->assigned_to }}"
                                                >
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                                    </svg>
                                                </button>
                                            @elseif($mt->status === 'in_progress')
                                                <!-- State 2: Sudah Dikerjakan - Blue Edit Work, Green Approve, Red Delete -->
                                                <button
                                                    type="button"
                                                    class="btn-edit-work inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50"
                                                    title="Edit Pengisian Pengerjaan"
                                                    data-action="{{ route('maintenance.work', $mt) }}"
                                                    data-completed-date="{{ $mt->completed_date?->toDateString() ?: now()->toDateString() }}"
                                                    data-work-start-time="{{ $mt->work_start_time }}"
                                                    data-work-end-time="{{ $mt->work_end_time }}"
                                                    data-assigned-to="{{ $mt->assigned_to }}"
                                                    data-resolution-notes="{{ $mt->resolution_notes }}"
                                                >
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M12 20h9"></path>
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                    </svg>
                                                </button>

                                                <form method="POST" action="{{ route('maintenance.approve', $mt) }}" class="inline" onsubmit="return confirm('Setujui pengerjaan ini? Status titik lampu akan otomatis kembali Aktif.');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-green-700 hover:bg-green-100" title="Approve Pekerjaan">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('maintenance.destroy', $mt) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100" title="Hapus">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M3 6h18"></path>
                                                            <path d="M8 6V4h8v2"></path>
                                                            <path d="M19 6l-1 14H6L5 6"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <!-- State 3: Completed/Approved - No Actions -->
                                                <span class="text-xs text-gray-400 italic">Terverifikasi</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-5 py-12 text-center text-sm text-gray-500">Data tiket maintenance tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Tab 2: Jadwal Maintenance (Board View) -->
        @if ($tab === 'jadwal')
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                
                <!-- HARI INI -->
                <div class="flex flex-col rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                    <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-2">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Hari Ini
                        </h4>
                        <span class="rounded bg-red-100 px-2 py-0.5 text-xs font-bold text-red-800">{{ count($groupedJadwal['hari_ini']) }}</span>
                    </div>
                    <div class="flex flex-col gap-3 overflow-y-auto max-h-[600px] no-scrollbar">
                        @forelse($groupedJadwal['hari_ini'] as $item)
                            @include('pages.sideral.partials.maintenance-card', ['item' => $item])
                        @empty
                            <p class="text-center text-xs text-gray-400 py-8 bg-white rounded border border-gray-100">Tidak ada jadwal hari ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- MINGGU INI -->
                <div class="flex flex-col rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                    <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-2">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            Minggu Ini
                        </h4>
                        <span class="rounded bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800">{{ count($groupedJadwal['minggu_ini']) }}</span>
                    </div>
                    <div class="flex flex-col gap-3 overflow-y-auto max-h-[600px] no-scrollbar">
                        @forelse($groupedJadwal['minggu_ini'] as $item)
                            @include('pages.sideral.partials.maintenance-card', ['item' => $item])
                        @empty
                            <p class="text-center text-xs text-gray-400 py-8 bg-white rounded border border-gray-100">Tidak ada jadwal minggu ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- MENDATANG -->
                <div class="flex flex-col rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                    <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-2">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                            Mendatang
                        </h4>
                        <span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-800">{{ count($groupedJadwal['mendatang']) }}</span>
                    </div>
                    <div class="flex flex-col gap-3 overflow-y-auto max-h-[600px] no-scrollbar">
                        @forelse($groupedJadwal['mendatang'] as $item)
                            @include('pages.sideral.partials.maintenance-card', ['item' => $item])
                        @empty
                            <p class="text-center text-xs text-gray-400 py-8 bg-white rounded border border-gray-100">Tidak ada jadwal mendatang.</p>
                        @endforelse
                    </div>
                </div>

                <!-- RIWAYAT SELESAI / BATAL -->
                <div class="flex flex-col rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                    <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-2">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            Riwayat
                        </h4>
                        <span class="rounded bg-green-100 px-2 py-0.5 text-xs font-bold text-green-800">{{ count($groupedJadwal['riwayat']) }}</span>
                    </div>
                    <div class="flex flex-col gap-3 overflow-y-auto max-h-[600px] no-scrollbar">
                        @forelse($groupedJadwal['riwayat'] as $item)
                            @include('pages.sideral.partials.maintenance-card', ['item' => $item])
                        @empty
                            <p class="text-center text-xs text-gray-400 py-8 bg-white rounded border border-gray-100">Belum ada riwayat pengerjaan.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        @endif

    </div>

    <!-- MAIN CRUD MODAL -->
    <div id="maintenanceModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-2xl rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 id="maintenanceModalTitle" class="text-lg font-semibold text-gray-800">Buat Maintenance</h3>
                    <p class="mt-1 text-sm text-gray-500">Form pembuatan tiket dan penjadwalan pemeliharaan.</p>
                </div>
                <button id="btnCloseMaintenanceModal" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">x</button>
            </div>

            <form id="maintenanceForm" method="POST" action="{{ route('maintenance.store') }}" class="space-y-4 px-5 py-5">
                @csrf
                <input id="maintenanceFormMethod" type="hidden" name="_method" value="PUT" disabled>
                <input id="mtStatus" type="hidden" name="status" value="pending">

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="mtBuilding" class="mb-1 block text-sm font-medium text-gray-700">Gedung</label>
                        <select id="mtBuilding" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="">Pilih Gedung</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="mtFloor" class="mb-1 block text-sm font-medium text-gray-700">Lantai</label>
                        <select id="mtFloor" name="floor_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="">Pilih Lantai</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="mtLamp" class="mb-1 block text-sm font-medium text-gray-700">Pilih Titik Lampu (Kode Lampu)</label>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <select id="mtLamp" name="lamp_id" class="w-full flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                                <option value="">-- Tanpa Titik Lampu --</option>
                            </select>
                            <button id="btnOpenFloorPlanPicker" type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-teal-600 bg-teal-50 px-4 text-sm font-semibold text-teal-700 hover:bg-teal-100 shrink-0">
                                <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Pilih dari Floor Plan
                            </button>
                        </div>
                        <div id="selectedLampBadge" class="mt-2 hidden items-center justify-between rounded-lg bg-teal-50 border border-teal-200 px-3 py-2 text-xs font-medium text-teal-800">
                            <span class="flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-teal-600"></span>
                                <span id="selectedLampInfo">Titik Lampu Dipilih: -</span>
                            </span>
                            <button id="btnClearSelectedLamp" type="button" class="text-teal-700 hover:text-teal-900 font-bold ml-2">Hapus</button>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="mtType" class="mb-1 block text-sm font-medium text-gray-700">Jenis Masalah / Pemeliharaan</label>
                        <select id="mtType" name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="Lampu Mati">Lampu Mati</option>
                            <option value="Lampu Flicker">Lampu Flicker</option>
                            <option value="Lampu Redup">Lampu Redup</option>
                            <option value="Pembersihan">Pembersihan Lampu</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="mtDescription" class="mb-1 block text-sm font-medium text-gray-700">Deskripsi Masalah</label>
                        <textarea id="mtDescription" name="description" rows="3" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Jelaskan detail permasalahan..."></textarea>
                    </div>
                    <div>
                        <label for="mtPriority" class="mb-1 block text-sm font-medium text-gray-700">Prioritas</label>
                        <select id="mtPriority" name="priority" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="low">Rendah (Low)</option>
                            <option value="medium" selected>Sedang (Medium)</option>
                            <option value="high">Tinggi (High)</option>
                        </select>
                    </div>
                    <div>
                        <label for="mtScheduledDate" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Dijadwalkan</label>
                        <input id="mtScheduledDate" name="scheduled_date" type="date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                    </div>
                    <div class="md:col-span-2">
                        <label for="mtAssignedTo" class="mb-1 block text-sm font-medium text-gray-700">User / Teknisi yang Ditunjuk (Opsional)</label>
                        <input id="mtAssignedTo" name="assigned_to" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Ahmad / Budi">
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button id="btnCancelMaintenanceModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- PROSES PENGERJAAN MODAL (Yellow Button Action / Edit Work Details) -->
    <div id="workModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 id="workModalTitle" class="text-base font-semibold text-gray-800">Proses Pengerjaan</h3>
                <button id="btnCloseWorkModal" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">x</button>
            </div>
            <form id="workForm" method="POST" class="space-y-4 px-5 py-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="workDate" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Dikerjakan</label>
                    <input id="workDate" name="completed_date" type="date" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="workStartTime" class="mb-1 block text-sm font-medium text-gray-700">Jam Mulai</label>
                        <input id="workStartTime" name="work_start_time" type="time" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                    </div>
                    <div>
                        <label for="workEndTime" class="mb-1 block text-sm font-medium text-gray-700">Jam Selesai</label>
                        <input id="workEndTime" name="work_end_time" type="time" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                    </div>
                </div>
                <div>
                    <label for="workAssignedTo" class="mb-1 block text-sm font-medium text-gray-700">Siapa yang Mengerjakan</label>
                    <input id="workAssignedTo" name="assigned_to" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Nama teknisi">
                </div>
                <div>
                    <label for="workResolutionNotes" class="mb-1 block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                    <textarea id="workResolutionNotes" name="resolution_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Catat detail pengerjaan..."></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button id="btnCancelWorkModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan Pekerjaan</button>
                </div>
            </form>
    </div>
    </div>

    <!-- FLOOR PLAN PICKER MODAL -->
    <div id="floorPlanPickerModal" class="fixed inset-0 z-[999999] hidden items-center justify-center bg-gray-900/60 p-4">
        <div class="flex flex-col w-full max-w-4xl max-h-[90vh] rounded-xl bg-white shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gray-50">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Pilih Titik Lampu dari Floor Plan</h3>
                    <p class="text-xs text-gray-500">Klik titik lampu pada denah lantai untuk memasukkannya ke form maintenance.</p>
                </div>
                <button id="btnCloseFloorPlanPicker" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-700">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Filter Bar inside Modal -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 bg-white px-6 py-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase text-gray-500">Gedung</label>
                        <select id="fpPickerBuilding" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 focus:border-teal-500 focus:outline-none">
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase text-gray-500">Lantai</label>
                        <select id="fpPickerFloor" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 focus:border-teal-500 focus:outline-none">
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-600">
                    <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-green-500"></span> Aktif</span>
                    <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-gray-500"></span> Mati</span>
                    <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-red-500"></span> Error</span>
                </div>
            </div>

            <!-- Floor Canvas Container -->
            <div class="relative flex-1 bg-slate-100 overflow-auto p-4 min-h-[380px] flex items-center justify-center">
                <div id="fpPickerContainer" class="relative max-w-full rounded border border-gray-200 bg-white shadow-inner overflow-hidden" style="min-width: 300px; min-height: 300px;">
                    <img id="fpPickerImage" src="" alt="Floor Plan" class="w-full h-auto object-contain hidden">
                    <div id="fpPickerDotsLayer" class="absolute inset-0"></div>
                    <div id="fpPickerEmptyState" class="p-12 text-center text-sm text-gray-400">
                        Pilih Gedung & Lantai untuk melihat denah titik lampu.
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-3 bg-gray-50">
                <div id="fpPickerSelectedText" class="text-xs font-semibold text-teal-700">
                    Belum ada titik lampu terpilih.
                </div>
                <div class="flex gap-2">
                    <button id="btnCancelFloorPlanPicker" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100">Batal</button>
                    <button id="btnConfirmFloorPlanPicker" type="button" disabled class="rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white hover:bg-teal-800 disabled:opacity-50">Gunakan Titik Ini</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allBuildings = @json($buildings);
    const allLamps = @json($lamps);
    const allRooms = [];

    // Main CRUD Modal Elements
    const modal = document.getElementById('maintenanceModal');
    const form = document.getElementById('maintenanceForm');
    const method = document.getElementById('maintenanceFormMethod');
    const title = document.getElementById('maintenanceModalTitle');

    const fields = {
        building: document.getElementById('mtBuilding'),
        floor: document.getElementById('mtFloor'),
        lamp: document.getElementById('mtLamp'),
        type: document.getElementById('mtType'),
        description: document.getElementById('mtDescription'),
        priority: document.getElementById('mtPriority'),
        scheduledDate: document.getElementById('mtScheduledDate'),
        assignedTo: document.getElementById('mtAssignedTo'),
        status: document.getElementById('mtStatus'),
    };

    const selectedLampBadge = document.getElementById('selectedLampBadge');
    const selectedLampInfo = document.getElementById('selectedLampInfo');
    const btnClearSelectedLamp = document.getElementById('btnClearSelectedLamp');
    const btnOpenFloorPlanPicker = document.getElementById('btnOpenFloorPlanPicker');

    // Floor Plan Picker Modal Elements
    const fpPickerModal = document.getElementById('floorPlanPickerModal');
    const fpPickerBuilding = document.getElementById('fpPickerBuilding');
    const fpPickerFloor = document.getElementById('fpPickerFloor');
    const fpPickerImage = document.getElementById('fpPickerImage');
    const fpPickerDotsLayer = document.getElementById('fpPickerDotsLayer');
    const fpPickerEmptyState = document.getElementById('fpPickerEmptyState');
    const fpPickerSelectedText = document.getElementById('fpPickerSelectedText');
    const btnConfirmFloorPlanPicker = document.getElementById('btnConfirmFloorPlanPicker');
    const btnCloseFloorPlanPicker = document.getElementById('btnCloseFloorPlanPicker');
    const btnCancelFloorPlanPicker = document.getElementById('btnCancelFloorPlanPicker');

    let tempSelectedLampId = null;

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ── Helper: Populate Floor Dropdown from Building ────────────────────────
    function populateFloors(buildingSelectEl, floorSelectEl, selectedFloorId = null) {
        const bId = buildingSelectEl.value;
        floorSelectEl.innerHTML = '<option value="">Pilih Lantai</option>';
        if (!bId) return;

        const building = allBuildings.find(b => b.id == bId);
        if (!building || !building.floors) return;

        building.floors.forEach(f => {
            const opt = document.createElement('option');
            opt.value = f.id;
            opt.textContent = f.name;
            if (selectedFloorId && f.id == selectedFloorId) {
                opt.selected = true;
            }
            floorSelectEl.appendChild(opt);
        });
    }

    // ── Helper: Populate Lamp Dropdown & Set Room ─────────────────────────────
    function updateLampsAndRoom(floorId, selectedLampId = null) {
        fields.lamp.innerHTML = '<option value="">-- Tanpa Titik Lampu --</option>';

        if (!floorId) {
            updateLampBadge(null);
            return;
        }

        // Populate lamps for this floor
        const floorLamps = allLamps.filter(l => l.floor_id == floorId);
        floorLamps.forEach(lamp => {
            const opt = document.createElement('option');
            opt.value = lamp.id;
            const typeName = lamp.lamp_type ? lamp.lamp_type.name : 'Lampu';
            opt.textContent = `${lamp.code} (${typeName})`;
            if (selectedLampId && lamp.id == selectedLampId) {
                opt.selected = true;
            }
            fields.lamp.appendChild(opt);
        });

        if (selectedLampId) {
            fields.lamp.value = selectedLampId;
            const chosenLamp = allLamps.find(l => l.id == selectedLampId);
            if (chosenLamp) {
                updateLampBadge(chosenLamp);
            }
        } else {
            updateLampBadge(null);
        }
    }

    function updateLampBadge(lamp) {
        if (!lamp) {
            selectedLampBadge.classList.add('hidden');
            selectedLampBadge.classList.remove('flex');
            selectedLampInfo.textContent = 'Titik Lampu Dipilih: -';
        } else {
            selectedLampBadge.classList.remove('hidden');
            selectedLampBadge.classList.add('flex');
            const areaName = lamp.floor ? `${lamp.floor.building?.name || ''} / ${lamp.floor.name || ''}` : '';
            selectedLampInfo.textContent = `Titik Lampu Dipilih: ${lamp.code} (${lamp.lamp_type?.name || 'Lampu'}) ${areaName ? ' - ' + areaName : ''}`;
        }
    }

    // ── Main Form Events ──────────────────────────────────────────────────────
    fields.building?.addEventListener('change', function () {
        populateFloors(fields.building, fields.floor);
        fields.room.value = '';
        updateLampsAndRoom(null);
    });

    fields.floor?.addEventListener('change', function () {
        updateLampsAndRoom(this.value);
    });

    fields.lamp?.addEventListener('change', function () {
        const lId = this.value;
        if (lId) {
            const lamp = allLamps.find(l => l.id == lId);
            if (lamp) {
                if (lamp.floor) {
                    fields.building.value = lamp.floor.building_id;
                    populateFloors(fields.building, fields.floor, lamp.floor_id);
                }
                updateLampBadge(lamp);
            }
        } else {
            updateLampBadge(null);
        }
    });

    btnClearSelectedLamp?.addEventListener('click', function () {
        fields.lamp.value = '';
        updateLampBadge(null);
    });

    // ── Floor Plan Picker Modal Logic ──────────────────────────────────────────
    function openFloorPlanPicker() {
        fpPickerModal.classList.remove('hidden');
        fpPickerModal.classList.add('flex');

        if (fields.building.value) {
            fpPickerBuilding.value = fields.building.value;
            populateFloors(fpPickerBuilding, fpPickerFloor, fields.floor.value);
        } else if (allBuildings.length > 0) {
            fpPickerBuilding.value = allBuildings[0].id;
            populateFloors(fpPickerBuilding, fpPickerFloor);
        }

        renderFloorPlanCanvas(fpPickerFloor.value, fields.lamp.value);
    }

    function closeFloorPlanPicker() {
        fpPickerModal.classList.add('hidden');
        fpPickerModal.classList.remove('flex');
    }

    fpPickerBuilding?.addEventListener('change', function () {
        populateFloors(fpPickerBuilding, fpPickerFloor);
        renderFloorPlanCanvas(fpPickerFloor.value);
    });

    fpPickerFloor?.addEventListener('change', function () {
        renderFloorPlanCanvas(this.value);
    });

    function renderFloorPlanCanvas(floorId, currentSelectedLampId = null) {
        fpPickerDotsLayer.innerHTML = '';
        tempSelectedLampId = currentSelectedLampId ? parseInt(currentSelectedLampId) : null;
        btnConfirmFloorPlanPicker.disabled = !tempSelectedLampId;

        if (!floorId) {
            fpPickerImage.classList.add('hidden');
            fpPickerEmptyState.classList.remove('hidden');
            fpPickerEmptyState.textContent = 'Silakan pilih Gedung & Lantai terlebih dahulu.';
            fpPickerSelectedText.textContent = 'Belum ada titik lampu terpilih.';
            return;
        }

        fetch(`{{ url('/floor-plan/data') }}?floor_id=${floorId}`)
            .then(res => res.json())
            .then(data => {
                const floor = data.floor;
                const lamps = data.lamps || [];

                if (floor && floor.floor_plan_image) {
                    fpPickerImage.src = floor.floor_plan_image;
                    fpPickerImage.classList.remove('hidden');
                    fpPickerEmptyState.classList.add('hidden');
                } else {
                    fpPickerImage.classList.add('hidden');
                    fpPickerEmptyState.classList.remove('hidden');
                    fpPickerEmptyState.textContent = 'Denah gambar belum diupload untuk lantai ini. Anda tetap dapat memilih titik lampu jika tersedia.';
                }

                if (lamps.length === 0 && (!floor || !floor.floor_plan_image)) {
                    fpPickerEmptyState.textContent = 'Belum ada gambar denah dan titik lampu di lantai ini.';
                }

                lamps.forEach(lamp => {
                    const dot = document.createElement('div');
                    const isSelected = tempSelectedLampId && tempSelectedLampId == lamp.id;

                    let bgClass = 'bg-green-500';
                    if (lamp.status === 'off') bgClass = 'bg-gray-500';
                    else if (lamp.status === 'rusak') bgClass = 'bg-red-500';

                    dot.className = `absolute cursor-pointer flex items-center justify-center transition-all duration-150 rounded-full text-[10px] font-bold text-white shadow-md hover:scale-125 hover:z-30 ${bgClass} ${isSelected ? 'ring-4 ring-teal-400 scale-125 z-30' : ''}`;
                    dot.style.left = `${lamp.position_x}%`;
                    dot.style.top = `${lamp.position_y}%`;
                    dot.style.width = '24px';
                    dot.style.height = '24px';
                    dot.style.transform = 'translate(-50%, -50%)';
                    dot.title = `${lamp.code} - ${lamp.lamp_type ? lamp.lamp_type.name : 'Lampu'}`;
                    dot.innerText = lamp.code ? lamp.code.replace('L-', '') : '';

                    dot.addEventListener('click', function (e) {
                        e.stopPropagation();
                        fpPickerDotsLayer.querySelectorAll('.ring-4').forEach(d => {
                            d.classList.remove('ring-4', 'ring-teal-400', 'scale-125', 'z-30');
                        });
                        dot.classList.add('ring-4', 'ring-teal-400', 'scale-125', 'z-30');
                        tempSelectedLampId = lamp.id;
                        btnConfirmFloorPlanPicker.disabled = false;
                        fpPickerSelectedText.textContent = `Terpilih: ${lamp.code} (${lamp.lamp_type ? lamp.lamp_type.name : 'Lampu'})`;
                    });

                    fpPickerDotsLayer.appendChild(dot);
                });

                if (tempSelectedLampId) {
                    const sel = lamps.find(l => l.id == tempSelectedLampId);
                    if (sel) {
                        fpPickerSelectedText.textContent = `Terpilih: ${sel.code} (${sel.lamp_type ? sel.lamp_type.name : 'Lampu'})`;
                    }
                } else {
                    fpPickerSelectedText.textContent = 'Klik pada titik lampu di denah untuk memilih.';
                }
            })
            .catch(err => {
                console.error(err);
                fpPickerImage.classList.add('hidden');
                fpPickerEmptyState.classList.remove('hidden');
                fpPickerEmptyState.textContent = 'Gagal memuat data floor plan.';
            });
    }

    btnOpenFloorPlanPicker?.addEventListener('click', openFloorPlanPicker);
    btnCloseFloorPlanPicker?.addEventListener('click', closeFloorPlanPicker);
    btnCancelFloorPlanPicker?.addEventListener('click', closeFloorPlanPicker);

    btnConfirmFloorPlanPicker?.addEventListener('click', function () {
        if (!tempSelectedLampId) return;

        const chosenLamp = allLamps.find(l => l.id == tempSelectedLampId);
        if (chosenLamp && chosenLamp.floor) {
            fields.building.value = chosenLamp.floor.building_id;
            populateFloors(fields.building, fields.floor, chosenLamp.floor_id);
            updateLampsAndRoom(chosenLamp.floor_id, chosenLamp.id);
        }
        closeFloorPlanPicker();
    });

    // ── Open Create Maintenance Modal ───────────────────────────────────────
    document.getElementById('btnOpenMaintenanceModal')?.addEventListener('click', function () {
        title.textContent = 'Buat Maintenance';
        form.action = @json(route('maintenance.store', ['tab' => $tab]));
        method.disabled = true;
        form.reset();

        fields.scheduledDate.value = new Date().toISOString().split('T')[0];

        if (allBuildings.length > 0) {
            fields.building.value = allBuildings[0].id;
            populateFloors(fields.building, fields.floor);
            if (allBuildings[0].floors && allBuildings[0].floors.length > 0) {
                fields.floor.value = allBuildings[0].floors[0].id;
                updateLampsAndRoom(fields.floor.value);
            }
        }
        openModal();
    });

    // ── Open Edit Maintenance Modal ─────────────────────────────────────────
    document.querySelectorAll('.btn-edit-maintenance').forEach(function (button) {
        button.addEventListener('click', function () {
            title.textContent = 'Edit Maintenance';
            form.action = this.dataset.action + '?tab=' + @json($tab);
            method.disabled = false;
            method.value = 'PUT';

            const floorId = this.dataset.floorId;
            const lampId = this.dataset.lampId;

            if (lampId) {
                const lamp = allLamps.find(l => l.id == lampId);
                if (lamp && lamp.floor) {
                    fields.building.value = lamp.floor.building_id;
                    populateFloors(fields.building, fields.floor, lamp.floor_id);
                    updateLampsAndRoom(lamp.floor_id, lamp.id);
                }
            } else if (floorId) {
                const floor = allBuildings.flatMap(b => b.floors || []).find(f => f.id == floorId);
                if (floor) {
                    fields.building.value = floor.building_id;
                    populateFloors(fields.building, fields.floor, floorId);
                    updateLampsAndRoom(floorId, null);
                }
            }

            fields.type.value = this.dataset.type || 'Lampu Mati';
            fields.description.value = this.dataset.description || '';
            fields.priority.value = this.dataset.priority || 'medium';
            if (fields.status) fields.status.value = this.dataset.status || 'pending';
            fields.scheduledDate.value = this.dataset.scheduledDate || new Date().toISOString().split('T')[0];
            fields.assignedTo.value = this.dataset.assignedTo || '';

            openModal();
        });
    });

    document.getElementById('btnCloseMaintenanceModal')?.addEventListener('click', closeModal);
    document.getElementById('btnCancelMaintenanceModal')?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });

    // ── Work Modal Elements ───────────────────────────────────────────────────
    const workModal = document.getElementById('workModal');
    const workForm = document.getElementById('workForm');
    const workTitle = document.getElementById('workModalTitle');

    const workFields = {
        date: document.getElementById('workDate'),
        startTime: document.getElementById('workStartTime'),
        endTime: document.getElementById('workEndTime'),
        assignedTo: document.getElementById('workAssignedTo'),
        notes: document.getElementById('workResolutionNotes'),
    };

    function openWorkModal() {
        workModal.classList.remove('hidden');
        workModal.classList.add('flex');
    }

    function closeWorkModal() {
        workModal.classList.add('hidden');
        workModal.classList.remove('flex');
    }

    document.querySelectorAll('.btn-process-work').forEach(function (button) {
        button.addEventListener('click', function () {
            workTitle.textContent = 'Proses Pengerjaan';
            workForm.action = this.dataset.action + '?tab=' + @json($tab);

            workForm.reset();
            workFields.date.value = new Date().toISOString().split('T')[0];
            workFields.assignedTo.value = this.dataset.assignedTo || '';

            const now = new Date();
            const startHour = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
            workFields.startTime.value = startHour;

            now.setHours(now.getHours() + 1);
            const endHour = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
            workFields.endTime.value = endHour;

            openWorkModal();
        });
    });

    document.querySelectorAll('.btn-edit-work').forEach(function (button) {
        button.addEventListener('click', function () {
            workTitle.textContent = 'Edit Pengisian Pengerjaan';
            workForm.action = this.dataset.action + '?tab=' + @json($tab);

            workFields.date.value = this.dataset.completedDate || new Date().toISOString().split('T')[0];
            workFields.startTime.value = this.dataset.workStartTime || '';
            workFields.endTime.value = this.dataset.workEndTime || '';
            workFields.assignedTo.value = this.dataset.assignedTo || '';
            workFields.notes.value = this.dataset.resolutionNotes || '';

            openWorkModal();
        });
    });

    document.getElementById('btnCloseWorkModal')?.addEventListener('click', closeWorkModal);
    document.getElementById('btnCancelWorkModal')?.addEventListener('click', closeWorkModal);
    workModal?.addEventListener('click', function (event) {
        if (event.target === workModal) closeWorkModal();
    });
});
</script>
@endpush
