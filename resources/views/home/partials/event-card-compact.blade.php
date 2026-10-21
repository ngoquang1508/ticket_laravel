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
                <x-icons.ticket class="h-10 w-10 text-gray-500" />
            </div>
        @endif

        @if(!$event->isSaleStarted())
            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-amber-500/90 text-white text-[10px] font-bold tracking-wide uppercase backdrop-blur-sm shadow-md z-10">
                Sắp mở bán
            </span>
        @endif
    </div>

    {{-- Thông tin sự kiện --}}
    <div class="flex flex-col pt-2.5">
        {{-- Tên sự kiện cố định 2 dòng để các card bằng nhau --}}
        <h3
            class="h-[2.6rem] line-clamp-2 text-sm font-semibold text-white group-hover:text-emerald-400 transition-colors leading-snug">
            {{ $event->name }}
        </h3>

        {{-- Giá vé khởi điểm --}}
        <div class="mt-1 text-xs sm:text-sm font-bold text-emerald-400">
            @if(isset($event->ticket_types_min_price) && $event->ticket_types_min_price > 0)
                Từ {{ number_format($event->ticket_types_min_price, 0, ',', '.') }}đ
            @else
                Từ 0đ
            @endif
        </div>

        {{-- Địa điểm (Thành phố) và Ngày diễn ra --}}
        <div class="mt-1 flex items-center text-xs text-gray-400">
            <x-icons.location class="mr-1 h-3.5 w-3.5 shrink-0 text-gray-400" />
            <span class="truncate">
                {{ $event->location ? $event->location->city : 'Toàn quốc' }}
            </span>
            <span class="mx-1 text-gray-500">•</span>
            <span class="shrink-0 font-medium">
                {{ $event->starts_at ? $event->starts_at->format('d/m') : 'Đang cập nhật' }}
            </span>
        </div>
    </div>
</a>