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
                <h3 class="text-lg font-semibold text-gray-800">Manajemen Jenis Lampu</h3>
                <p class="mt-1 text-sm text-gray-500">Kelola katalog master jenis lampu, spesifikasi watt, bentuk model (Bulat / Panjang TL), dan estimasi harga.</p>
            </div>
            <button id="btnOpenLampTypeModal" type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                Tambah Jenis Lampu
            </button>
        </div>

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

    </div>

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
@endpush
