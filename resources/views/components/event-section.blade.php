@props(['events', 'title', 'type', 'isPast' => false, 'categorySlug' => null, 'centerTitle' => false])

@if ($events->isNotEmpty())
    <!-- Title Section -->
    <div class="mb-4 flex items-center gap-2 {{ $isPast ? 'mt-8' : '' }} {{ $centerTitle ? 'justify-center mb-8' : '' }}">
        @if(!$centerTitle)
            @if ($isPast)
                <x-icons.archive class="h-6 w-6" />
            @else
                <x-icons.clock class="h-6 w-6 text-green-500" />
            @endif
        @endif
        <h2 class="font-bold">{{ $title }}</h2>
    </div>

    <!-- Grid Event Cards -->
    <div id="{{ $type }}-events-container"
        class="mb-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 {{ $isPast ? 'opacity-60' : '' }}">
        @foreach($events as $event)
            <x-event-card :event="$event" />
        @endforeach
    </div>

    <!-- Button Load More Event -->
    @if($events instanceof \Illuminate\Contracts\Pagination\Paginator && $events->hasMorePages())
        <div class="mb-12 text-center" id="{{ $type }}-load-more-wrapper">
            <button
                class="load-more-btn inline-flex h-10 items-center justify-center overflow-hidden rounded-full border px-6 text-sm font-semibold transition-all duration-300 ease-in-out cursor-pointer {{ $isPast ? 'border-gray-500 text-gray-400 hover:bg-gray-700' : 'border-green-500 text-green-500 hover:bg-green-500' }} hover:text-white"
                data-type="{{ $type }}" data-page="2" data-url="{{ route('events.index', array_filter(['categorySlug' => $categorySlug, 'q' => request('q')])) }}">
                <span class="flex items-center gap-1">
                    Xem thêm sự kiện {{ $type === 'upcoming' ? 'sắp diễn ra' : 'đã diễn ra' }}
                    <x-icons.chevron-down class="h-4 w-4" />
                </span>
            </button>
        </div>
    @else
        <div class="mb-12"></div>
    @endif
@endif