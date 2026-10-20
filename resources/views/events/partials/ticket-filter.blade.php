{{--
Component Bộ lọc sự kiện phong cách Ticketbox
Bao gồm:
- Bên trái: Tiêu đề "Sự kiện" (hoặc tên thể loại hiện tại)
- Bên phải: 2 Nút lọc [Chọn ngày] & [Bộ lọc]
- Popovers: Nhúng từ events/partials/filter/{date-popover, main-popover}
- Javascript: Điều khiển qua module resources/js/components/ticketbox-filter.js
--}}

@php
    // Xác định nhãn hiển thị cho nút Thời gian
    $dateLabel = 'Tất cả các ngày';
    $hasDateFilter = false;

    if (!empty($startDate)) {
        $hasDateFilter = true;
        if (!empty($endDate) && $endDate !== $startDate) {
            $dateLabel = \Carbon\Carbon::parse($startDate)->format('d/m') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
        } else {
            $dateLabel = \Carbon\Carbon::parse($startDate)->format('d/m/Y');
        }
    } elseif (!empty($datePreset) && $datePreset !== 'all') {
        $hasDateFilter = true;
        $dateLabel = match ($datePreset) {
            'today' => 'Hôm nay',
            'tomorrow' => 'Ngày mai',
            'weekend' => 'Cuối tuần này',
            'month' => 'Tháng này',
            default => 'Tất cả các ngày'
        };
    }

    // Đếm số lượng bộ lọc tổng hợp đang áp dụng
    $activeFilterCount = 0;
    if (!empty($locationFilter) && $locationFilter !== 'all') {
        $activeFilterCount++;
    }
    if ($isFree) {
        $activeFilterCount++;
    }
    if (!empty($selectedCategories)) {
        $activeFilterCount += count($selectedCategories);
    }
    $hasMainFilter = $activeFilterCount > 0;

    $eventsBaseUrl = route('events.index');
    $serializedCategories = json_encode(!empty($selectedCategories) ? array_values($selectedCategories) : (isset($currentCategory) ? [$currentCategory->slug] : []));
@endphp

<div class="mb-6 flex justify-end sm:flex-row sm:items-center sm:justify-between gap-4 relative z-30"
    id="ticketbox-filter-wrapper" data-events-url="{{ $eventsBaseUrl }}" data-date-preset="{{ $datePreset ?? 'all' }}"
    data-start-date="{{ $startDate ?? '' }}" data-end-date="{{ $endDate ?? '' }}"
    data-selected-categories="{{ $serializedCategories }}">

    {{-- Bên trái: Tiêu đề Sự kiện --}}
    <div class="hidden sm:block sm:flex items-center gap-3">
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight">
            {{ isset($currentCategory) ? $currentCategory->name : 'Sự kiện' }}
        </h1>
    </div>

    {{-- Bên phải: Thanh 2 nút kích hoạt bộ lọc --}}
    <div class="flex items-center gap-3 relative">
        {{-- Nút 1: Lọc Thời gian --}}
        <div class="relative inline-block text-left">
            @if($hasDateFilter)
                <div
                    class="inline-flex items-center rounded-full bg-[#10b981] text-white shadow-md shadow-emerald-500/20 text-xs sm:text-sm font-semibold transition hover:bg-[#059669]">
                    <button type="button" id="btn-toggle-date-filter"
                        class="inline-flex items-center gap-2 pl-4 pr-2 py-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="max-w-[130px] sm:max-w-[180px] truncate">{{ $dateLabel }}</span>
                    </button>
                    <a href="{{ request()->fullUrlWithQuery(['date_preset' => null, 'start_date' => null, 'end_date' => null]) }}"
                        class="pr-3 pl-1 py-2 text-white/80 hover:text-white transition" title="Xóa lọc thời gian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            @else
                <button type="button" id="btn-toggle-date-filter"
                    class="inline-flex items-center gap-2 rounded-full bg-white/10 hover:bg-white/15 text-white border border-white/10 px-4 py-2 text-xs sm:text-sm font-semibold transition cursor-pointer backdrop-blur-md">
                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Tất cả các ngày</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif

            {{-- Popover Lịch & Presets ngày --}}
            @include('events.partials.filter.date-popover')
        </div>

        {{-- Nút 2: Lọc Tổng hợp (Vị trí, Giá, Thể loại) --}}
        <div class="relative inline-block text-left">
            @if($hasMainFilter)
                <div
                    class="inline-flex items-center rounded-full bg-[#10b981] text-white shadow-md shadow-emerald-500/20 text-xs sm:text-sm font-semibold transition hover:bg-[#059669]">
                    <button type="button" id="btn-toggle-main-filter"
                        class="inline-flex items-center gap-2 pl-4 pr-2 py-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Bộ lọc ({{ $activeFilterCount }})</span>
                    </button>
                    <a href="{{ route('events.index', request()->except(['location', 'is_free', 'categories'])) }}"
                        class="pr-3 pl-1 py-2 text-white/80 hover:text-white transition" title="Xóa bộ lọc">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            @else
                <button type="button" id="btn-toggle-main-filter"
                    class="inline-flex items-center gap-2 rounded-full bg-white/10 hover:bg-white/15 text-white border border-white/10 px-4 py-2 text-xs sm:text-sm font-semibold transition cursor-pointer backdrop-blur-md">
                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Bộ lọc</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif

            {{-- Popover Bộ lọc Tổng hợp (Vị trí, Giá, Thể loại) --}}
            @include('events.partials.filter.main-popover')
        </div>
    </div>
</div>