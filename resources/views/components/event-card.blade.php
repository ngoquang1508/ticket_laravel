@props(['event'])

<a href="{{ route('events.show', $event->slug) }}"
    class="group flex flex-col overflow-hidden transition-transform duration-300">
    <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-gray-800">
        @if($event->image)
            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-800 to-gray-950">
                <x-icons.ticket class="h-12 w-12 text-gray-500" />
            </div>
        @endif

        @if(!$event->isSaleStarted())
            <span
                class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-amber-500/90 text-white text-[11px] font-bold tracking-wide uppercase backdrop-blur-sm shadow-md z-10">
                Sắp mở bán
            </span>
        @endif
    </div>
    <div class="flex flex-col pt-3">
        <h3 class="line-clamp-2 text-base font-bold">{{ $event->name }}</h3>

        <div class="mt-2 text-sm font-semibold text-green-500">
            @if(isset($event->ticket_types_min_price) && $event->ticket_types_min_price > 0)
                Từ {{ number_format($event->ticket_types_min_price, 0, ',', '.') }}đ
            @else
                Từ 0đ
            @endif
        </div>

        <div class="mt-1 flex items-center text-sm text-gray-400">
            <x-icons.calendar />
            <span>{{ $event->starts_at->format('d \t\h\á\n\g m, Y') }}</span>
        </div>
    </div>
</a>