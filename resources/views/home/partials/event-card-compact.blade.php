@props(['event'])

{{-- Card sự kiện phong cách Ticketbox: Ảnh 16:9, Tên sự kiện, Giá vé màu xanh lá, Địa điểm & Ngày --}}
<a href="{{ route('events.show', $event->slug) }}"
    class="group flex flex-col overflow-hidden">
    {{-- Khung ảnh sự kiện 16:9 --}}
    <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-gray-800">
        @if($event->image)
            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-800 to-gray-950">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Thông tin sự kiện --}}
    <div class="flex flex-col pt-3">
        {{-- Tên sự kiện tối đa 2 dòng --}}
        <h3
            class="line-clamp-2 text-sm sm:text-base font-bold text-white group-hover:text-emerald-400 transition-colors">
            {{ $event->name }}
        </h3>

        {{-- Giá vé khởi điểm --}}
        <div class="mt-1.5 text-sm sm:text-base font-bold text-emerald-400">
            @if(isset($event->ticket_types_min_price) && $event->ticket_types_min_price > 0)
                Từ {{ number_format($event->ticket_types_min_price, 0, ',', '.') }}đ
            @else
                Từ 0đ
            @endif
        </div>

        {{-- Địa điểm (Thành phố) và Ngày diễn ra --}}
        <div class="mt-1 flex items-center text-sm font-semibold sm:text-sm text-white/70">
            <svg class="mr-1 h-3.5 w-3.5 shrink-0 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate">
                {{ $event->location ? $event->location->city : 'Toàn quốc' }}
            </span>
            <span class="mx-1.5 text-gray-500">•</span>
            <span class="shrink-0">
                {{ $event->starts_at ? $event->starts_at->format('d \T\h m') : 'Đang cập nhật' }}
            </span>
        </div>
    </div>
</a>