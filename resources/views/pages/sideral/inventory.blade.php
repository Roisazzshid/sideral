@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Jenis Lampu" />

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

        <!-- Card Header -->
        <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Inventory</h3>
                <p class="mt-1 text-sm text-gray-500">Kelola katalog jenis lampu dan titik lampu yang terpasang di seluruh fasilitas gedung.</p>
            </div>
            <div id="btnGroupJenisLampu">
                <button id="btnOpenLampTypeModal" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                    Tambah Jenis Lampu
                </button>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex flex-wrap gap-2 rounded-lg border border-gray-200 bg-white p-2">
            <a href="{{ route('inventory', ['tab' => 'jenis-lampu']) }}"
               class="rounded-lg px-4 py-2 text-sm font-semibold {{ $tab === 'jenis-lampu' ? 'bg-teal-700 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                Jenis Lampu
            </a>
            <a href="{{ route('inventory', ['tab' => 'lampu-terpasang']) }}"
               class="rounded-lg px-4 py-2 text-sm font-semibold {{ $tab === 'lampu-terpasang' ? 'bg-teal-700 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                Lampu Terpasang
            </a>
        </div>
        {{-- ================ TAB JENIS LAMPU ================ --}}
        @if($tab === 'jenis-lampu')
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('inventory') }}" class="rounded-lg border border-gray-200 bg-white px-5 py-4">
            <div class="grid gap-3 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Cari Jenis Lampu</label>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama, tipe, watt..." class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Bentuk Model</label>
                    <select name="shape" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                        <option value="">Semua Bentuk</option>
                        <option value="bulat" @selected($shapeFilter === 'bulat')>Bulet ⚪</option>
                        <option value="segitiga" @selected($shapeFilter === 'segitiga')>Segitiga 🔺</option>
                        <option value="garis" @selected($shapeFilter === 'garis')>Garis ▬</option>
                        <option value="persegi_panjang" @selected($shapeFilter === 'persegi_panjang')>Persegi Panjang █</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Status</label>
                        <select name="status" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="">Semua Status</option>
                            <option value="aktif" @selected($statusFilter === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected($statusFilter === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="h-10 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">Filter</button>
                </div>
            </div>
        </form>

        <!-- Main Table -->
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-5 py-4">
                <h4 class="font-semibold text-gray-800">Daftar Jenis Lampu</h4>
                <p class="mt-1 text-sm text-gray-500">Master data jenis dan tipe lampu yang digunakan pada fasilitas.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="w-12 px-5 py-3 text-center">No</th>
                            <th class="px-5 py-3">Nama Lampu</th>
                            <th class="px-5 py-3">Bentuk Model</th>
                            <th class="px-5 py-3">Tipe Lampu</th>
                            <th class="px-5 py-3 text-center">Titik Terpasang</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lampTypes as $index => $type)
                            <tr>
                                <td class="px-5 py-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-800">{{ $type->name }}</div>
                                    @if($type->description)
                                        <div class="text-xs text-gray-500 max-w-xs truncate" title="{{ $type->description }}">{{ $type->description }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($type->shape === 'segitiga')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">
                                            <span class="h-2 w-2 rounded-full bg-amber-600"></span> Segitiga 🔺
                                        </span>
                                    @elseif($type->shape === 'garis')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                            <span class="h-1 w-3 rounded-xs bg-emerald-600"></span> Garis ▬
                                        </span>
                                    @elseif($type->shape === 'persegi_panjang')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700 ring-1 ring-purple-200">
                                            <span class="h-2.5 w-3.5 rounded-xs bg-purple-600"></span> Persegi Panjang █
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-200">
                                            <span class="h-2 w-2 rounded-full bg-blue-600"></span> Bulet ⚪
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-medium text-gray-800">
                                    {{ $type->type }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="rounded bg-teal-50 px-2 py-1 text-xs font-bold text-teal-700">{{ number_format($type->lamps_count) }} Titik</span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($type->status === 'aktif')
                                        <span class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-green-200">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-gray-200">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                class="btn-edit-lamp-type inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700"
                                                data-action="{{ route('inventory.lamp-type.update', $type) }}"
                                                data-name="{{ $type->name }}"
                                                data-type="{{ $type->type }}"
                                                data-shape="{{ $type->shape }}"
                                                data-status="{{ $type->status }}"
                                                data-description="{{ $type->description }}"
                                                title="Edit Jenis Lampu">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <form method="POST" action="{{ route('inventory.lamp-type.destroy', $type) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis lampu ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                                    title="Hapus Jenis Lampu">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-400">Belum ada jenis lampu terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- end tab jenis-lampu --}}
    @endif

    {{-- ================ TAB LAMPU TERPASANG ================ --}}
    @if($tab === 'lampu-terpasang')
    <div class="space-y-4">
        <!-- Filter -->
        <form method="GET" action="{{ route('inventory') }}" class="rounded-lg border border-gray-200 bg-white px-5 py-4">
            <input type="hidden" name="tab" value="lampu-terpasang">
            <div class="grid gap-3 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Cari Kode / Jenis Lampu</label>
                    <input type="search" name="lamp_search" value="{{ request('lamp_search') }}" placeholder="Cari kode lampu..." class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Status Lampu</label>
                    <select name="lamp_status" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                        <option value="">Semua Status</option>
                        <option value="on" @selected(request('lamp_status') === 'on')>Aktif (On)</option>
                        <option value="off" @selected(request('lamp_status') === 'off')>Mati (Off)</option>
                        <option value="rusak" @selected(request('lamp_status') === 'rusak')>Rusak</option>
                        <option value="perbaikan" @selected(request('lamp_status') === 'perbaikan')>Dalam Perbaikan</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="h-10 w-full rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">Filter</button>
                </div>
            </div>
        </form>

        <!-- Table Lampu Terpasang -->
        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-5 py-4">
                <h4 class="font-semibold text-gray-800">Daftar Lampu Terpasang</h4>
                <p class="mt-1 text-sm text-gray-500">Semua titik lampu yang terpasang beserta lokasi dan statusnya.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="w-12 px-5 py-3 text-center">No</th>
                            <th class="px-5 py-3">Kode Lampu</th>
                            <th class="px-5 py-3">Jenis Lampu</th>
                            <th class="px-5 py-3">Gedung</th>
                            <th class="px-5 py-3">Lantai</th>
                            <th class="px-5 py-3 text-center">Posisi (X, Y)</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $filteredLamps = $lamps;
                            if(request('lamp_search')) {
                                $s = strtolower(request('lamp_search'));
                                $filteredLamps = $filteredLamps->filter(fn($l) =>
                                    str_contains(strtolower($l->code), $s) ||
                                    str_contains(strtolower($l->lampType?->name ?? ''), $s)
                                );
                            }
                            if(request('lamp_status')) {
                                $filteredLamps = $filteredLamps->where('status', request('lamp_status'));
                            }
                        @endphp
                        @forelse($filteredLamps as $idx => $lamp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 text-center font-medium text-gray-500">{{ $idx + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-gray-800">{{ $lamp->code }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $lamp->lampType?->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-700">{{ $lamp->floor?->building?->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-700">{{ $lamp->floor?->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="rounded bg-gray-100 px-2 py-1 text-xs font-mono text-gray-600">{{ $lamp->position_x }}, {{ $lamp->position_y }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($lamp->status === 'on')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-green-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Aktif
                                        </span>
                                    @elseif($lamp->status === 'off')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-gray-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Mati
                                        </span>
                                    @elseif($lamp->status === 'rusak')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Rusak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Perbaikan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- History -->
                                        <button type="button"
                                            class="btn-lamp-history inline-flex h-8 w-8 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-700 hover:bg-violet-100"
                                            title="Lihat Histori"
                                            data-id="{{ $lamp->id }}"
                                            data-url="{{ route('inventory.lamp.history', $lamp) }}"
                                            data-code="{{ $lamp->code }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="1 4 1 10 7 10"></polyline>
                                                <path d="M3.51 15a9 9 0 1 0 .49-3.51"></path>
                                            </svg>
                                        </button>
                                        <!-- Edit -->
                                        <button type="button"
                                            class="btn-edit-lamp inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50"
                                            title="Edit Titik Lampu"
                                            data-action="{{ route('inventory.lamp.update', $lamp) }}"
                                            data-code="{{ $lamp->code }}"
                                            data-status="{{ $lamp->status }}"
                                            data-installed-date="{{ $lamp->installed_date?->format('Y-m-d') }}"
                                            data-notes="{{ $lamp->notes }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('inventory.lamp.destroy', $lamp) }}" onsubmit="return confirm('Hapus titik lampu {{ $lamp->code }}? Aksi ini tidak bisa dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                title="Hapus Titik Lampu">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada titik lampu terpasang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL CRUD JENIS LAMPU -->
    <div id="lampTypeModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 id="lampTypeModalTitle" class="text-base font-semibold text-gray-800">Tambah Jenis Lampu</h3>
                <button id="btnCloseLampTypeModal" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">x</button>
            </div>
            <form id="lampTypeForm" method="POST" action="{{ route('inventory.lamp-type.store') }}" class="space-y-4 px-5 py-5">
                @csrf
                <input id="lampTypeFormMethod" type="hidden" name="_method" value="PUT" disabled>

                <div>
                    <label for="ltName" class="mb-1 block text-sm font-medium text-gray-700">Nama Jenis Lampu</label>
                    <input id="ltName" name="name" type="text" required placeholder="Contoh: Philips Downlight LED" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="ltType" class="mb-1 block text-sm font-medium text-gray-700">Tipe Lampu</label>
                        <select id="ltType" name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="Downlight">Downlight</option>
                            <option value="LED Tube">LED Tube (TL)</option>
                            <option value="LED Bulb">LED Bulb</option>
                            <option value="Panel">Panel Light</option>
                            <option value="Spotlight">Spotlight</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="ltShape" class="mb-1 block text-sm font-medium text-gray-700">Bentuk Model</label>
                        <select id="ltShape" name="shape" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="bulat">Bulet ⚪</option>
                            <option value="segitiga">Segitiga 🔺</option>
                            <option value="garis">Garis ▬</option>
                            <option value="persegi_panjang">Persegi Panjang █</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="ltStatus" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select id="ltStatus" name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label for="ltDescription" class="mb-1 block text-sm font-medium text-gray-700">Deskripsi / Spesifikasi</label>
                    <textarea id="ltDescription" name="description" rows="2" placeholder="Catatan spesifikasi atau detail barang..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100"></textarea>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button id="btnCancelLampTypeModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HISTORI LAMPU -->
    <div id="lampHistoryModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/60 p-4">
        <div class="w-full max-w-2xl rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Histori Maintenance</h3>
                    <p id="lampHistorySubtitle" class="text-sm text-gray-500 mt-0.5"></p>
                </div>
                <button id="btnCloseLampHistoryModal" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 max-h-[60vh] overflow-y-auto">
                <div id="lampHistoryLoading" class="py-8 text-center text-sm text-gray-400">
                    <svg class="mx-auto mb-2 animate-spin h-6 w-6 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Memuat histori...
                </div>
                <div id="lampHistoryContent" class="hidden">
                    <h5 class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-500">Riwayat Maintenance</h5>
                    <div id="lampHistoryTable" class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Tanggal</th>
                                    <th class="px-4 py-2.5 text-left">Jenis</th>
                                    <th class="px-4 py-2.5 text-left">Teknisi</th>
                                    <th class="px-4 py-2.5 text-left">Status</th>
                                    <th class="px-4 py-2.5 text-left">Selesai</th>
                                    <th class="px-4 py-2.5 text-left">Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="lampHistoryTableBody" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                    <p id="lampHistoryEmpty" class="hidden py-6 text-center text-sm text-gray-400">Belum ada riwayat maintenance untuk lampu ini.</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-gray-100 px-5 py-3">
                <button id="btnCloseLampHistoryModal2" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT TITIK LAMPU -->
    <div id="editLampModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/60 p-4">
        <div class="w-full max-w-md rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 class="text-base font-semibold text-gray-800">Edit Titik Lampu</h3>
                <button id="btnCloseEditLampModal" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="editLampForm" method="POST" class="space-y-4 px-5 py-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="elCode" class="mb-1 block text-sm font-medium text-gray-700">Kode Lampu</label>
                    <input id="elCode" name="code" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label for="elStatus" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select id="elStatus" name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                        <option value="on">Aktif (On)</option>
                        <option value="off">Mati (Off)</option>
                        <option value="rusak">Rusak</option>
                        <option value="perbaikan">Dalam Perbaikan</option>
                    </select>
                </div>
                <div>
                    <label for="elInstalledDate" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Pemasangan</label>
                    <input id="elInstalledDate" name="installed_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                </div>
                <div>
                    <label for="elNotes" class="mb-1 block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea id="elNotes" name="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Catatan opsional..."></textarea>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button id="btnCancelEditLampModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('lampTypeModal');
    const form = document.getElementById('lampTypeForm');
    const method = document.getElementById('lampTypeFormMethod');
    const title = document.getElementById('lampTypeModalTitle');

    const fields = {
        name: document.getElementById('ltName'),
        type: document.getElementById('ltType'),
        shape: document.getElementById('ltShape'),
        status: document.getElementById('ltStatus'),
        description: document.getElementById('ltDescription'),
    };

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('btnOpenLampTypeModal')?.addEventListener('click', function () {
        title.textContent = 'Tambah Jenis Lampu';
        form.action = @json(route('inventory.lamp-type.store'));
        method.disabled = true;
        form.reset();
        fields.shape.value = 'bulat';
        fields.status.value = 'aktif';
        openModal();
    });

    document.querySelectorAll('.btn-edit-lamp-type').forEach(function (button) {
        button.addEventListener('click', function () {
            title.textContent = 'Edit Jenis Lampu';
            form.action = this.dataset.action;
            method.disabled = false;
            method.value = 'PUT';

            fields.name.value = this.dataset.name || '';
            fields.type.value = this.dataset.type || 'Downlight';
            fields.shape.value = this.dataset.shape || 'bulat';
            fields.status.value = this.dataset.status || 'aktif';
            fields.description.value = this.dataset.description || '';

            openModal();
        });
    });

    document.getElementById('btnCloseLampTypeModal')?.addEventListener('click', closeModal);
    document.getElementById('btnCancelLampTypeModal')?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });
});
</script>

<script>
// ── Lamp History Modal ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const histModal   = document.getElementById('lampHistoryModal');
    const histLoading = document.getElementById('lampHistoryLoading');
    const histContent = document.getElementById('lampHistoryContent');
    const histEmpty   = document.getElementById('lampHistoryEmpty');
    const histTbody   = document.getElementById('lampHistoryTableBody');
    const histSubtitle= document.getElementById('lampHistorySubtitle');

    function openHistoryModal() { histModal.classList.remove('hidden'); histModal.classList.add('flex'); }
    function closeHistoryModal() { histModal.classList.add('hidden'); histModal.classList.remove('flex'); }

    document.querySelectorAll('.btn-lamp-history').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url  = this.dataset.url;
            const code = this.dataset.code;

            histSubtitle.textContent = 'Lampu: ' + code;
            histLoading.classList.remove('hidden');
            histContent.classList.add('hidden');
            histEmpty.classList.add('hidden');
            histTbody.innerHTML = '';
            openHistoryModal();

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    histLoading.classList.add('hidden');
                    histContent.classList.remove('hidden');

                    const rows = data.maintenances || [];
                    if (rows.length === 0) {
                        document.getElementById('lampHistoryTable').classList.add('hidden');
                        histEmpty.classList.remove('hidden');
                    } else {
                        document.getElementById('lampHistoryTable').classList.remove('hidden');
                        const statusLabels = {
                            pending: '<span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">Pending</span>',
                            in_progress: '<span class="rounded-full bg-orange-50 px-2 py-0.5 text-xs font-semibold text-orange-700">Dikerjakan</span>',
                            completed: '<span class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700">Selesai</span>',
                            cancelled: '<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">Batal</span>',
                        };
                        rows.forEach(function (m) {
                            const tr = document.createElement('tr');
                            tr.className = 'text-gray-700 hover:bg-gray-50';
                            tr.innerHTML = `
                                <td class="px-4 py-2.5 text-xs">${m.scheduled_date || '-'}</td>
                                <td class="px-4 py-2.5 text-xs">${m.type || '-'}</td>
                                <td class="px-4 py-2.5 text-xs font-medium">${m.technician || '-'}</td>
                                <td class="px-4 py-2.5">${statusLabels[m.status] || m.status}</td>
                                <td class="px-4 py-2.5 text-xs">${m.completed_date || '-'}</td>
                                <td class="px-4 py-2.5 text-xs text-gray-500 max-w-xs truncate">${m.notes || '-'}</td>
                            `;
                            histTbody.appendChild(tr);
                        });
                    }
                })
                .catch(() => {
                    histLoading.classList.add('hidden');
                    histContent.classList.remove('hidden');
                    histEmpty.textContent = 'Gagal memuat histori. Coba lagi.';
                    histEmpty.classList.remove('hidden');
                });
        });
    });

    document.getElementById('btnCloseLampHistoryModal')?.addEventListener('click', closeHistoryModal);
    document.getElementById('btnCloseLampHistoryModal2')?.addEventListener('click', closeHistoryModal);
    histModal?.addEventListener('click', function (e) { if (e.target === histModal) closeHistoryModal(); });

    // ── Edit Lamp Modal ────────────────────────────────────────────────────────
    const editModal = document.getElementById('editLampModal');
    const editForm  = document.getElementById('editLampForm');

    function openEditModal() { editModal.classList.remove('hidden'); editModal.classList.add('flex'); }
    function closeEditModal() { editModal.classList.add('hidden'); editModal.classList.remove('flex'); }

    document.querySelectorAll('.btn-edit-lamp').forEach(function (btn) {
        btn.addEventListener('click', function () {
            editForm.action = this.dataset.action;
            document.getElementById('elCode').value   = this.dataset.code   || '';
            document.getElementById('elStatus').value = this.dataset.status || 'on';
            document.getElementById('elInstalledDate').value = this.dataset.installedDate || '';
            document.getElementById('elNotes').value  = this.dataset.notes  || '';
            openEditModal();
        });
    });

    document.getElementById('btnCloseEditLampModal')?.addEventListener('click', closeEditModal);
    document.getElementById('btnCancelEditLampModal')?.addEventListener('click', closeEditModal);
    editModal?.addEventListener('click', function (e) { if (e.target === editModal) closeEditModal(); });

    // Hide tambah jenis lampu button on lamp tab
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab === 'lampu-terpasang') {
        document.getElementById('btnGroupJenisLampu')?.classList.add('hidden');
    }
});
</script>
@endpush
