@props(['events', 'title', 'type', 'isPast' => false, 'categorySlug' => null, 'centerTitle' => false])

@if ($events->isNotEmpty())
    <!-- Title Section -->
    <div class="mb-4 flex items-center gap-2 {{ $isPast ? 'mt-8' : '' }} {{ $centerTitle ? 'justify-center mb-8' : '' }}">
        @if(!$centerTitle)
            <svg class="h-6 w-6 {{ $isPast ? '' : 'text-green-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                @if ($isPast)
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                @endif
            </svg>
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
                data-type="{{ $type }}" data-page="2" data-url="{{ route('events.index', $categorySlug) }}">
                <span class="flex items-center gap-1">
                    Xem thêm sự kiện {{ $type === 'upcoming' ? 'sắp diễn ra' : 'đã diễn ra' }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </button>
        </div>
    @else
        <div class="mb-12"></div>
    @endif
@endif