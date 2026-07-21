@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Lighting" />

    <div class="space-y-5">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
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

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">Total Jenis Lampu</p>
                <p class="mt-2 text-2xl font-semibold text-gray-800">{{ number_format($totalTypes) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">Total Stok</p>
                <p class="mt-2 text-2xl font-semibold text-teal-700">{{ number_format($totalStock) }} <span class="text-sm font-medium text-gray-500">Buah</span></p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">Tipe Aktif</p>
                <p class="mt-2 text-2xl font-semibold text-green-700">{{ number_format($activeTypes) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">Butuh Perhatian</p>
                <p class="mt-2 text-2xl font-semibold text-orange-600">{{ number_format($lowStockTypes) }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Data Lampu</h3>
                    <p class="mt-1 text-sm text-gray-500">Katalog tipe lampu, watt, stok gudang, dan status ketersediaan.</p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <form method="GET" action="{{ route('lighting') }}" class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-3.5-3.5"></path>
                            </svg>
                        </span>
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari jenis lampu..."
                            class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100 sm:w-72"
                        >
                    </form>

                    <button id="btnOpenCreateLamp" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 text-sm font-semibold text-white hover:bg-teal-800">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                        </svg>
                        Tambah Lampu
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="w-16 px-5 py-3">No</th>
                            <th class="px-5 py-3">Nama Lampu</th>
                            <th class="px-5 py-3">Jenis</th>
                            <th class="px-5 py-3">Watt</th>
                            <th class="px-5 py-3">Stok</th>
                            <th class="px-5 py-3">Satuan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="w-28 px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lampTypes as $index => $lampType)
                            @php
                                $stock = $lampType->inventory?->stock_quantity ?? 0;
                                $stockStatus = $stockStatusResolver($lampType);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-800">{{ $lampType->name }}</div>
                                    <div class="mt-0.5 text-xs text-gray-500">{{ number_format($lampType->lamps_count) }} titik terpasang</div>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $lampType->type }}</td>
                                <td class="px-5 py-4 text-gray-700">{{ number_format($lampType->watt) }} W</td>
                                <td class="px-5 py-4 font-semibold text-gray-800">{{ number_format($stock) }}</td>
                                <td class="px-5 py-4 text-gray-700">Buah</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $stockStatus['class'] }}">
                                        {{ $stockStatus['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="btn-edit-lamp inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-teal-200 hover:bg-teal-50 hover:text-teal-700"
                                            title="Edit"
                                            data-action="{{ route('lighting.update', $lampType) }}"
                                            data-name="{{ $lampType->name }}"
                                            data-type="{{ $lampType->type }}"
                                            data-watt="{{ $lampType->watt }}"
                                            data-price="{{ $lampType->price }}"
                                            data-stock="{{ $stock }}"
                                            data-min-stock="{{ $lampType->inventory?->min_stock ?? 10 }}"
                                            data-status="{{ $lampType->status }}"
                                            data-description="{{ $lampType->description }}"
                                        >
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                            </svg>
                                            <span class="sr-only">Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('lighting.destroy', $lampType) }}" onsubmit="return confirm('Hapus data lampu ini? Titik lampu terkait juga akan ikut terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600" title="Delete">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 6h18"></path>
                                                    <path d="M8 6V4h8v2"></path>
                                                    <path d="M19 6l-1 14H6L5 6"></path>
                                                </svg>
                                                <span class="sr-only">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-500">
                                    Data lampu tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="lampModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-2xl rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 id="lampModalTitle" class="text-lg font-semibold text-gray-800">Tambah Lampu</h3>
                    <p class="mt-1 text-sm text-gray-500">Lengkapi spesifikasi lampu dan stok awal gudang.</p>
                </div>
                <button id="btnCloseLampModal" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="Tutup">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18 18 6"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="lampForm" method="POST" action="{{ route('lighting.store') }}" class="px-5 py-5">
                @csrf
                <input id="lampFormMethod" type="hidden" name="_method" value="PUT" disabled>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="lampName" class="mb-1 block text-sm font-medium text-gray-700">Nama Lampu</label>
                        <input id="lampName" name="name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Philips LED Tube 18W">
                    </div>
                    <div>
                        <label for="lampType" class="mb-1 block text-sm font-medium text-gray-700">Jenis</label>
                        <input id="lampType" name="type" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="LED Tube">
                    </div>
                    <div>
                        <label for="lampWatt" class="mb-1 block text-sm font-medium text-gray-700">Watt</label>
                        <input id="lampWatt" name="watt" type="number" min="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="18">
                    </div>
                    <div>
                        <label for="lampPrice" class="mb-1 block text-sm font-medium text-gray-700">Harga</label>
                        <input id="lampPrice" name="price" type="number" min="0" step="100" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="45000">
                    </div>
                    <div>
                        <label for="lampStock" class="mb-1 block text-sm font-medium text-gray-700">Stok</label>
                        <input id="lampStock" name="stock_quantity" type="number" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="320">
                    </div>
                    <div>
                        <label for="lampMinStock" class="mb-1 block text-sm font-medium text-gray-700">Minimum Stok</label>
                        <input id="lampMinStock" name="min_stock" type="number" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="20">
                    </div>
                    <div>
                        <label for="lampStatus" class="mb-1 block text-sm font-medium text-gray-700">Status Tipe</label>
                        <select id="lampStatus" name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div>
                        <label for="lampDescription" class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="lampDescription" name="description" rows="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" placeholder="Catatan opsional"></textarea>
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button id="btnCancelLampModal" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('lampModal');
    const form = document.getElementById('lampForm');
    const methodInput = document.getElementById('lampFormMethod');
    const title = document.getElementById('lampModalTitle');

    const fields = {
        name: document.getElementById('lampName'),
        type: document.getElementById('lampType'),
        watt: document.getElementById('lampWatt'),
        price: document.getElementById('lampPrice'),
        stock: document.getElementById('lampStock'),
        minStock: document.getElementById('lampMinStock'),
        status: document.getElementById('lampStatus'),
        description: document.getElementById('lampDescription'),
    };

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('btnOpenCreateLamp').addEventListener('click', function () {
        title.textContent = 'Tambah Lampu';
        form.action = @json(route('lighting.store'));
        methodInput.disabled = true;
        form.reset();
        fields.status.value = 'aktif';
        fields.minStock.value = 10;
        fields.stock.value = 0;
        openModal();
    });

    document.querySelectorAll('.btn-edit-lamp').forEach(function (button) {
        button.addEventListener('click', function () {
            title.textContent = 'Edit Lampu';
            form.action = this.dataset.action;
            methodInput.disabled = false;
            methodInput.value = 'PUT';

            fields.name.value = this.dataset.name || '';
            fields.type.value = this.dataset.type || '';
            fields.watt.value = this.dataset.watt || '';
            fields.price.value = this.dataset.price || '';
            fields.stock.value = this.dataset.stock || 0;
            fields.minStock.value = this.dataset.minStock || 10;
            fields.status.value = this.dataset.status || 'aktif';
            fields.description.value = this.dataset.description || '';
            openModal();
        });
    });

    document.getElementById('btnCloseLampModal').addEventListener('click', closeModal);
    document.getElementById('btnCancelLampModal').addEventListener('click', closeModal);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });
});
</script>
@endpush
