@extends('layouts.app')

@php
    $tabs = [
        'gedung' => 'Gedung',
        'lantai' => 'Lantai',
        'teknisi' => 'Teknisi',
    ];

    $roomTypes = [
        'office' => 'Office / Kantor',
        'lobby' => 'Lobby',
        'meeting_room' => 'Meeting Room',
        'toilet' => 'Toilet',
        'pantry' => 'Pantry',
        'server_room' => 'Server Room',
        'lounge' => 'Lounge',
        'utility' => 'Ruang Utilitas / Mesin',
        'storage' => 'Gudang / Arsip',
        'worship' => 'Tempat Ibadah / Musholla',
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb pageTitle="Master Data" />

    <div class="space-y-5">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Data belum bisa disimpan.</p>
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
                <h3 class="text-lg font-semibold text-gray-800">Master Data</h3>
                <p class="mt-1 text-sm text-gray-500">Konfigurasi administratif struktur bangunan dan ruangan fasilitas.</p>
            </div>

            @if($tab === 'gedung')
                <button id="btnOpenBuildingModal" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                    + Tambah Gedung
                </button>
            @elseif($tab === 'lantai')
                <button id="btnOpenFloorModal" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                    + Tambah Lantai
                </button>
            @elseif($tab === 'teknisi')
                <button id="btnOpenTechnicianModal" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                    + Tambah Teknisi
                </button>
            @endif
        </div>

        <!-- Tabs Navigation -->
        <div class="flex flex-wrap gap-2 rounded-lg border border-gray-200 bg-white p-2">
            @foreach($tabs as $key => $label)
                <a href="{{ route('master-data', ['tab' => $key]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold {{ $tab === $key ? 'bg-teal-700 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Tab 1: Gedung -->
        @if($tab === 'gedung')
            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h4 class="font-semibold text-gray-800">Data Gedung</h4>
                    <p class="mt-1 text-sm text-gray-500">Daftar gedung perkantoran atau fasilitas utama yang terdaftar.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                            <tr>
                                <th class="w-16 px-5 py-3">No</th>
                                <th class="px-5 py-3">Nama Gedung</th>
                                <th class="px-5 py-3">Lokasi</th>
                                <th class="px-5 py-3 text-right">Jumlah Lantai</th>
                                <th class="px-5 py-3 text-right">Jumlah Titik Lampu</th>
                                <th class="w-28 px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($buildings as $index => $b)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4 font-semibold text-gray-800">{{ $b->name }}</td>
                                    <td class="px-5 py-4 text-gray-700">{{ $b->location }}</td>
                                    <td class="px-5 py-4 text-right font-medium text-gray-800">{{ $b->floors->count() }} Lantai</td>
                                    <td class="px-5 py-4 text-right font-bold text-teal-700">{{ number_format($b->total_lamps) }} Titik</td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="btn-edit-building inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700"
                                                data-action="{{ route('master-data.building.update', $b) }}"
                                                data-name="{{ $b->name }}"
                                                data-location="{{ $b->location }}"
                                                data-description="{{ $b->description }}"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('master-data.building.destroy', $b) }}" onsubmit="return confirm('Apakah Anda yakin? Menghapus Gedung juga akan menghapus Lantai dan Ruangan terkait.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 6h18"></path>
                                                        <path d="M8 6V4h8v2"></path>
                                                        <path d="M19 6l-1 14H6L5 6"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500">Data gedung tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Tab 2: Lantai -->
        @if($tab === 'lantai')
            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h4 class="font-semibold text-gray-800">Data Lantai</h4>
                    <p class="mt-1 text-sm text-gray-500">Konfigurasi lantai spasial per gedung.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                            <tr>
                                <th class="w-16 px-5 py-3">No</th>
                                <th class="px-5 py-3">Gedung</th>
                                <th class="px-5 py-3">Nama Lantai</th>
                                <th class="px-5 py-3 text-right">Nomor Lantai</th>
                                <th class="px-5 py-3 text-right">Jumlah Titik Lampu</th>
                                <th class="w-28 px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($floors as $index => $f)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4 font-semibold text-gray-800">{{ $f->building?->name ?? '-' }}</td>
                                    <td class="px-5 py-4 text-gray-700 font-medium">{{ $f->name }}</td>
                                    <td class="px-5 py-4 text-right text-gray-700">{{ $f->floor_number }}</td>
                                    <td class="px-5 py-4 text-right font-bold text-teal-700">{{ $f->lamps->count() }} Titik</td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="btn-edit-floor inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700"
                                                data-action="{{ route('master-data.floor.update', $f) }}"
                                                data-building-id="{{ $f->building_id }}"
                                                data-name="{{ $f->name }}"
                                                data-floor-number="{{ $f->floor_number }}"
                                                data-description="{{ $f->description }}"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('master-data.floor.destroy', $f) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lantai ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 6h18"></path>
                                                        <path d="M8 6V4h8v2"></path>
                                                        <path d="M19 6l-1 14H6L5 6"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500">Data lantai tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Tab 3: Teknisi -->
        @if($tab === 'teknisi')
            <div class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h4 class="font-semibold text-gray-800">Data Teknisi</h4>
                    <p class="mt-1 text-sm text-gray-500">Daftar akun teknisi yang terdaftar untuk penugasan pemeliharaan.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                            <tr>
                                <th class="w-16 px-5 py-3">No</th>
                                <th class="px-5 py-3">Nama Teknisi</th>
                                <th class="px-5 py-3">Email</th>
                                <th class="px-5 py-3">Penempatan Gedung</th>
                                <th class="px-5 py-3">Password</th>
                                <th class="px-5 py-3">Tanggal Dibuat</th>
                                <th class="w-28 px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($technicians as $index => $t)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4 font-semibold text-gray-800">{{ $t->name }}</td>
                                    <td class="px-5 py-4 text-gray-600">{{ $t->email }}</td>
                                    <td class="px-5 py-4 text-gray-600">
                                        <span class="inline-flex items-center gap-1 rounded bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-800">
                                            {{ $t->building ? $t->building->name : 'Umum / Semua Gedung' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <span id="pwText-{{ $t->id }}" class="font-mono text-gray-600">••••••</span>
                                            <button type="button" 
                                                    onclick="toggleTablePassword(this, '{{ $t->id }}', '{{ $t->plain_password ?? 'password' }}')" 
                                                    class="text-gray-400 hover:text-gray-600 focus:outline-none"
                                                    title="Tampilkan / Sembunyikan Password">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-500">{{ $t->created_at ? $t->created_at->format('d/m/Y') : '-' }}</td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" 
                                                    data-action="{{ route('master-data.technician.update', $t->id) }}"
                                                    data-name="{{ $t->name }}"
                                                    data-email="{{ $t->email }}"
                                                    data-password="{{ $t->plain_password ?? 'password' }}"
                                                    data-building-id="{{ $t->building_id }}"
                                                    class="btn-edit-technician inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('master-data.technician.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus teknisi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 6h18"></path>
                                                        <path d="M8 6V4h8v2"></path>
                                                        <path d="M19 6l-1 14H6L5 6"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500">Data teknisi tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

    <!-- MODAL 3: Technician (Teknisi) -->
    <div id="technicianModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 id="technicianModalTitle" class="text-lg font-semibold text-gray-800">Tambah Teknisi</h3>
                <button id="btnCloseTechnicianModal" type="button" class="text-gray-400 hover:text-gray-600">x</button>
            </div>
            <form id="technicianForm" method="POST" action="{{ route('master-data.technician.store') }}" class="space-y-4 px-5 py-5">
                @csrf
                <input id="technicianFormMethod" type="hidden" name="_method" value="PUT" disabled>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Teknisi</label>
                    <input id="technicianName" name="name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input id="technicianEmail" name="email" type="email" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Penempatan Gedung (Opsional)</label>
                    <select id="technicianBuilding" name="building_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                        <option value="">Umum / Semua Gedung</option>
                        @foreach($buildings as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input id="technicianPassword" name="password" type="password" class="w-full rounded-lg border border-gray-300 pl-3 pr-10 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Minimal 6 karakter">
                        <button type="button" id="btnTogglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button id="btnCancelTechnicianModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 1: Building (Gedung) -->
    <div id="buildingModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 id="buildingModalTitle" class="text-lg font-semibold text-gray-800">Tambah Gedung</h3>
                <button id="btnCloseBuildingModal" type="button" class="text-gray-400 hover:text-gray-600">x</button>
            </div>
            <form id="buildingForm" method="POST" action="{{ route('master-data.building.store') }}" class="space-y-4 px-5 py-5">
                @csrf
                <input id="buildingFormMethod" type="hidden" name="_method" value="PUT" disabled>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Gedung</label>
                    <input id="buildingName" name="name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Lokasi / Alamat</label>
                    <input id="buildingLocation" name="location" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="buildingDesc" name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100"></textarea>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button id="btnCancelBuildingModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Floor (Lantai) -->
    <div id="floorModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 id="floorModalTitle" class="text-lg font-semibold text-gray-800">Tambah Lantai</h3>
                <button id="btnCloseFloorModal" type="button" class="text-gray-400 hover:text-gray-600">x</button>
            </div>
            <form id="floorForm" method="POST" action="{{ route('master-data.floor.store') }}" class="space-y-4 px-5 py-5">
                @csrf
                <input id="floorFormMethod" type="hidden" name="_method" value="PUT" disabled>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Gedung</label>
                    <select id="floorBuilding" name="building_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                        @foreach($buildings as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Lantai</label>
                    <input id="floorName" name="name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Lantai 1, Lantai Rooftop">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nomor Lantai (Urutan Fisik)</label>
                    <input id="floorNumber" name="floor_number" type="number" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="floorDesc" name="description" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100"></textarea>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button id="btnCancelFloorModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    function initModal(modalId, btnOpenId, btnCloseId, btnCancelId, formId, methodId, titleId, titleAdd, titleEdit, onOpenAction) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        const btnOpen = document.getElementById(btnOpenId);
        const btnClose = document.getElementById(btnCloseId);
        const btnCancel = document.getElementById(btnCancelId);
        const form = document.getElementById(formId);
        const method = document.getElementById(methodId);
        const title = document.getElementById(titleId);

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        if (btnOpen) {
            btnOpen.addEventListener('click', function () {
                title.textContent = titleAdd;
                method.disabled = true;
                form.reset();
                if (onOpenAction) onOpenAction(false);
                openModal();
            });
        }

        if (btnClose) btnClose.addEventListener('click', closeModal);
        if (btnCancel) btnCancel.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeModal();
        });

        return { openModal, closeModal, form, method, title };
    }

    // 1. Building Modal Setup
    const bModal = initModal('buildingModal', 'btnOpenBuildingModal', 'btnCloseBuildingModal', 'btnCancelBuildingModal', 'buildingForm', 'buildingFormMethod', 'buildingModalTitle', 'Tambah Gedung', 'Edit Gedung');
    document.querySelectorAll('.btn-edit-building').forEach(function (button) {
        button.addEventListener('click', function () {
            bModal.title.textContent = 'Edit Gedung';
            bModal.form.action = this.dataset.action;
            bModal.method.disabled = false;
            bModal.method.value = 'PUT';

            document.getElementById('buildingName').value = this.dataset.name || '';
            document.getElementById('buildingLocation').value = this.dataset.location || '';
            document.getElementById('buildingDesc').value = this.dataset.description || '';

            bModal.openModal();
        });
    });

    // 2. Floor Modal Setup
    const fModal = initModal('floorModal', 'btnOpenFloorModal', 'btnCloseFloorModal', 'btnCancelFloorModal', 'floorForm', 'floorFormMethod', 'floorModalTitle', 'Tambah Lantai', 'Edit Lantai');
    document.querySelectorAll('.btn-edit-floor').forEach(function (button) {
        button.addEventListener('click', function () {
            fModal.title.textContent = 'Edit Lantai';
            fModal.form.action = this.dataset.action;
            fModal.method.disabled = false;
            fModal.method.value = 'PUT';

            document.getElementById('floorBuilding').value = this.dataset.buildingId || '';
            document.getElementById('floorName').value = this.dataset.name || '';
            document.getElementById('floorNumber').value = this.dataset.floorNumber || '';
            document.getElementById('floorDesc').value = this.dataset.description || '';

            fModal.openModal();
        });
    });

    // 3. Technician Modal Setup
    const btnTogglePassword = document.getElementById('btnTogglePassword');
    const technicianPassword = document.getElementById('technicianPassword');

    function resetPasswordToggle() {
        if (technicianPassword) technicianPassword.type = 'password';
        if (btnTogglePassword) {
            btnTogglePassword.innerHTML = `
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `;
        }
    }

    const tModal = initModal('technicianModal', 'btnOpenTechnicianModal', 'btnCloseTechnicianModal', 'btnCancelTechnicianModal', 'technicianForm', 'technicianFormMethod', 'technicianModalTitle', 'Tambah Teknisi', 'Edit Teknisi', function(isEdit) {
        const pwField = document.getElementById('technicianPassword');
        resetPasswordToggle();
        if (isEdit) {
            pwField.required = false;
            pwField.placeholder = "Kosongkan jika tidak diubah";
        } else {
            pwField.required = true;
            pwField.placeholder = "Minimal 6 karakter";
            const bField = document.getElementById('technicianBuilding');
            if (bField) bField.value = '';
        }
    });

    document.querySelectorAll('.btn-edit-technician').forEach(function (button) {
        button.addEventListener('click', function () {
            tModal.title.textContent = 'Edit Teknisi';
            tModal.form.action = this.dataset.action;
            tModal.method.disabled = false;
            tModal.method.value = 'PUT';

            document.getElementById('technicianName').value = this.dataset.name || '';
            document.getElementById('technicianEmail').value = this.dataset.email || '';
            document.getElementById('technicianPassword').value = this.dataset.password || '';
            const bField = document.getElementById('technicianBuilding');
            if (bField) bField.value = this.dataset.buildingId || '';

            tModal.openModal();
            resetPasswordToggle();
            const pwField = document.getElementById('technicianPassword');
            pwField.required = false;
            pwField.placeholder = "Kosongkan jika tidak diubah";
        });
    });

    window.toggleTablePassword = function(button, id, plainPassword) {
        const span = document.getElementById(`pwText-${id}`);
        if (!span) return;
        
        if (span.textContent === '••••••') {
            span.textContent = plainPassword;
            button.innerHTML = `
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
            `;
        } else {
            span.textContent = '••••••';
            button.innerHTML = `
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `;
        }
    };

    btnTogglePassword?.addEventListener('click', function () {
        if (technicianPassword.type === 'password') {
            technicianPassword.type = 'text';
            this.innerHTML = `
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
            `;
        } else {
            technicianPassword.type = 'password';
            this.innerHTML = `
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `;
        }
    });

});
</script>
@endpush
