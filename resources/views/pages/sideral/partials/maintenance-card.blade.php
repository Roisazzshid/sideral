<div class="flex flex-col justify-between rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
    <div class="space-y-3">
        <!-- Date and Priority -->
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-500">
                {{ $item->scheduled_date?->format('d M Y') ?: 'Tidak Terjadwal' }}
            </span>
            @if($item->priority === 'high')
                <span class="inline-flex items-center rounded bg-red-50 px-1.5 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/10">High</span>
            @elseif($item->priority === 'medium')
                <span class="inline-flex items-center rounded bg-amber-50 px-1.5 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/15">Medium</span>
            @else
                <span class="inline-flex items-center rounded bg-emerald-50 px-1.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10">Low</span>
            @endif
        </div>

        <!-- Room Information -->
        <div>
            <div class="text-xs text-gray-400 font-medium">Area / Ruangan</div>
            <div class="text-sm font-bold text-gray-800">{{ $item->room?->name ?? '-' }}</div>
            <div class="text-xs text-gray-500 font-medium">{{ $item->room?->floor?->building?->name ?? '-' }} / {{ $item->room?->floor?->name ?? '-' }}</div>
            @if($item->lamp)
                <div class="mt-1">
                    <span class="inline-flex items-center rounded-md bg-teal-50 px-2 py-0.5 text-xs font-semibold text-teal-700 ring-1 ring-inset ring-teal-600/10">
                        Lampu: {{ $item->lamp->code }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Issue Title and Description -->
        <div>
            <div class="text-xs text-gray-400 font-medium">Jenis Masalah</div>
            <div class="text-sm font-semibold text-gray-800">{{ $item->type }}</div>
            @if($item->description)
                <p class="mt-1 text-xs text-gray-600 line-clamp-2" title="{{ $item->description }}">{{ $item->description }}</p>
            @endif
        </div>

        <!-- Assigned Technician -->
        @if($item->assigned_to)
            <div class="flex items-center gap-2 pt-1">
                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-100 text-teal-700 text-[10px] font-bold">
                    {{ strtoupper(substr($item->assigned_to, 0, 1)) }}
                </div>
                <span class="text-xs text-gray-600 font-medium">Teknisi: {{ $item->assigned_to }}</span>
            </div>
        @endif

        <!-- Resolution Notes (if completed) -->
        @if($item->status === 'completed' && $item->resolution_notes)
            <div class="rounded bg-green-50 p-2 text-xs border border-green-100">
                <span class="font-bold text-green-800">Penyelesaian:</span>
                <span class="text-green-700">{{ $item->resolution_notes }}</span>
            </div>
        @endif
    </div>

    <!-- Quick Action Button -->
    <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3">
        <button
            type="button"
            class="btn-edit-maintenance text-xs font-semibold text-teal-600 hover:text-teal-800 hover:underline"
            data-action="{{ route('maintenance.update', $item) }}"
            data-room-id="{{ $item->room_id }}"
            data-lamp-id="{{ $item->lamp_id }}"
            data-type="{{ $item->type }}"
            data-description="{{ $item->description }}"
            data-priority="{{ $item->priority }}"
            data-status="{{ $item->status }}"
            data-scheduled-date="{{ $item->scheduled_date?->toDateString() }}"
            data-completed-date="{{ $item->completed_date?->toDateString() }}"
            data-assigned-to="{{ $item->assigned_to }}"
            data-resolution-notes="{{ $item->resolution_notes }}"
        >
            Rincian & Edit
        </button>

        @if($item->status === 'pending')
            <button
                type="button"
                class="btn-process-work inline-flex items-center gap-1 rounded bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-100 border border-amber-200"
                data-action="{{ route('maintenance.work', $item) }}"
                data-work-date="{{ now()->toDateString() }}"
                data-assigned-to="{{ $item->assigned_to }}"
            >
                Proses Kerja
            </button>
        @elseif($item->status === 'in_progress')
            <div class="flex items-center gap-1.5">
                <button
                    type="button"
                    class="btn-edit-work inline-flex items-center rounded bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 border border-blue-200"
                    data-action="{{ route('maintenance.work', $item) }}"
                    data-completed-date="{{ $item->completed_date?->toDateString() ?: now()->toDateString() }}"
                    data-work-start-time="{{ $item->work_start_time }}"
                    data-work-end-time="{{ $item->work_end_time }}"
                    data-assigned-to="{{ $item->assigned_to }}"
                    data-resolution-notes="{{ $item->resolution_notes }}"
                >
                    Edit Kerja
                </button>
                <form method="POST" action="{{ route('maintenance.approve', $item) }}" class="inline" onsubmit="return confirm('Setujui pengerjaan ini? Stok lampu akan otomatis berkurang.');">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 hover:bg-green-100 border border-green-200">
                        Approve
                    </button>
                </form>
            </div>
        @else
            <span class="text-[11px] font-bold uppercase tracking-wider {{ $item->status === 'completed' ? 'text-green-600' : 'text-gray-400' }}">
                {{ $item->status === 'completed' ? 'Selesai' : 'Batal' }}
            </span>
        @endif
    </div>
</div>
