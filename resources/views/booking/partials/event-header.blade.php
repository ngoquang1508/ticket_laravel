{{-- Tiêu đề sự kiện và nút điều hướng quay lại --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-white mb-1">{{ $event->name }}</h1>
        <p class="text-gray-400 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ \Carbon\Carbon::parse($event->starts_at)->format('H:i, d \T\h\á\n\g m, Y') }}</span>
        </p>
    </div>
    <a href="{{ route('events.show', $event->slug) }}"
        class="text-sm font-semibold text-gray-300 hover:text-white bg-[#2b2b2b] px-4 py-2 rounded-lg transition self-start md:self-auto">
        Quay lại sự kiện
    </a>
</div>
