@props(['event'])

<a href="{{ route('events.show', $event->slug) }}"
    class="group flex flex-col overflow-hidden transition-transform duration-300">
    <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-gray-800">
        @if($event->image)
            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-800 to-gray-950">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
        @endif
    </div>
    <div class="flex flex-col pt-3">
        <h3 class="line-clamp-2 text-base font-bold">{{ $event->name }}</h3>

        <div class="mt-2 text-sm font-semibold text-green-500">
            @if(isset($event->ticket_types_min_price) && $event->ticket_types_min_price > 0)
                Từ {{ number_format($event->ticket_types_min_price, 0, ',', '.') }}đ
            @else
                Đang cập nhật
            @endif
        </div>

        <div class="mt-1 flex items-center text-sm text-gray-400">
            <svg class="mr-1.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ $event->starts_at->format('d \t\h\á\n\g m, Y') }}</span>
        </div>
    </div>
</a>