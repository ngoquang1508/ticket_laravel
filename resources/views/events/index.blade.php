@extends('layouts.app')

@section('title', isset($currentCategory) ? $currentCategory->name . ' - Sự kiện' : 'Tất cả sự kiện')

@section('content')

    @include('components.event-category-nav')

    <div
        class="mx-auto max-w-7xl px-4 pt-8 {{ $pastEvents->isEmpty() && $upcomingEvents->isEmpty() ? 'pb-8' : '' }} sm:px-6 lg:px-8 text-white">

        {{-- Bộ lọc sự kiện phong cách Ticketbox (Lọc ngày, địa điểm, miễn phí, thể loại) --}}
        @include('events.partials.ticket-filter')

        @php
            $hasSearch = !empty($searchTerm);
            $hasDateFilter = !empty($startDate) || (!empty($datePreset) && $datePreset !== 'all');
            $hasLocationFilter = !empty($locationFilter) && $locationFilter !== 'all';
            $hasCategoryFilter = !empty($selectedCategories);
            $hasMainFilter = $hasLocationFilter || $isFree || $hasCategoryFilter;
            $hasFilter = $hasDateFilter || $hasMainFilter;

            // Đếm tổng số tiêu chí lọc đang áp dụng ngoài từ khóa
            $activeFilterCount = ($hasLocationFilter ? 1 : 0) + ($isFree ? 1 : 0) + count($selectedCategories ?? []) + ($hasDateFilter ? 1 : 0);
        @endphp

        {{-- Thanh thông báo kết quả tìm kiếm & bộ lọc --}}
        @if($hasSearch)
            <div
                class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-gray-400">Kết quả tìm kiếm cho:</span>
                    <span class="text-base font-bold text-emerald-400">"{{ $searchTerm }}"</span>
                    @if($hasFilter)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300 border border-emerald-500/20">
                            kết hợp {{ $activeFilterCount }} bộ lọc
                        </span>
                    @endif
                    <span class="text-xs text-gray-400">({{ $upcomingEvents->total() + $pastEvents->total() }} sự kiện)</span>
                </div>

                <div class="flex items-center gap-3">
                    @if($hasFilter)
                        {{-- Nút chỉ xóa từ khóa, giữ lại các bộ lọc --}}
                        <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors"
                            title="Bỏ từ khóa và giữ lại bộ lọc">
                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Bỏ từ khóa</span>
                        </a>
                        <span class="text-white/20">|</span>
                    @endif

                    {{-- Nút xóa toàn bộ để xem tất cả sự kiện --}}
                    <a href="{{ isset($currentCategory) && !$hasFilter ? route('events.index', $currentCategory->slug) : route('events.index') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-white transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>{{ $hasFilter ? 'Xóa tất cả' : 'Xóa bộ lọc tìm kiếm' }}</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Sắp diễn ra --}}
        <x-event-section :events="$upcomingEvents" title="Sắp diễn ra" type="upcoming" :is-past="false"
            :category-slug="$currentCategory ? $currentCategory->slug : null" />

        @if($upcomingEvents->isNotEmpty() && $pastEvents->isNotEmpty())
            <div class="h-px w-full bg-gray-400/50"></div>
        @endif

        {{-- Đã diễn ra --}}
        <x-event-section :events="$pastEvents" title="Đã diễn ra" type="past" :is-past="true"
            :category-slug="$currentCategory ? $currentCategory->slug : null" />

        {{-- Trạng thái trống (Empty State) --}}
        @if($upcomingEvents->isEmpty() && $pastEvents->isEmpty())
            <div class="flex flex-col items-center justify-center text-center text-gray-400 py-8">
                @if($hasSearch && $hasFilter)
                    {{-- Trường hợp 1: Vừa có từ khóa vừa có bộ lọc --}}
                    <div
                        class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/5 border border-white/10 text-emerald-400 shadow-inner">
                        <x-icons.search class="h-8 w-8 text-emerald-400" />
                    </div>
                    <p class="text-base font-semibold text-white">Không tìm thấy sự kiện nào phù hợp</p>
                    <p class="mt-1.5 max-w-md text-xs sm:text-sm text-gray-400">
                        Không có sự kiện nào khớp với từ khóa <span class="font-medium text-emerald-400">"{{ $searchTerm }}"</span> kết hợp với các bộ lọc bạn đã chọn.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/20 transition-colors">
                            Chỉ bỏ từ khóa "{{ $searchTerm }}"
                        </a>
                        <a href="{{ route('events.index', ['q' => $searchTerm]) }}"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/20 transition-colors">
                            Chỉ tìm theo từ khóa (Bỏ bộ lọc)
                        </a>
                        <a href="{{ route('events.index') }}"
                            class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-2 text-xs font-bold text-black hover:bg-emerald-400 transition-colors shadow-lg shadow-emerald-500/20">
                            Xem tất cả sự kiện
                        </a>
                    </div>

                @elseif($hasSearch)
                    {{-- Trường hợp 2: Chỉ có từ khóa tìm kiếm --}}
                    <div
                        class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/5 border border-white/10 text-emerald-400 shadow-inner">
                        <x-icons.search class="h-8 w-8 text-emerald-400" />
                    </div>
                    <p class="text-base font-semibold text-white">Không tìm thấy sự kiện nào phù hợp với "{{ $searchTerm }}"</p>
                    <p class="mt-1 text-xs text-gray-400">Hãy thử kiểm tra lại chính tả hoặc tìm kiếm bằng từ khóa khác.</p>
                    <a href="{{ route('events.index') }}"
                        class="mt-5 inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-2.5 text-xs font-bold text-black hover:bg-emerald-400 transition-colors shadow-lg shadow-emerald-500/20">
                        Xem tất cả sự kiện
                    </a>

                @elseif($hasFilter)
                    {{-- Trường hợp 3: Chỉ có bộ lọc (không có từ khóa) --}}
                    <div
                        class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/5 border border-white/10 text-emerald-400 shadow-inner">
                        <svg class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <p class="text-base font-semibold text-white">Không tìm thấy sự kiện nào theo bộ lọc</p>
                    <p class="mt-1.5 max-w-md text-xs sm:text-sm text-gray-400">
                        Chưa có sự kiện nào phù hợp với tiêu chí thời gian, địa điểm hoặc mức giá bạn đã chọn.
                    </p>
                    <a href="{{ route('events.index') }}"
                        class="mt-5 inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-2.5 text-xs font-bold text-black hover:bg-emerald-400 transition-colors shadow-lg shadow-emerald-500/20">
                        Xóa bộ lọc & xem tất cả
                    </a>

                @else
                    {{-- Trường hợp 4: Danh mục hoặc trang sự kiện mặc định chưa có dữ liệu --}}
                    <p class="my-6 text-sm font-medium text-gray-300">Chưa có vé
                        {{ $currentCategory ? 'cho "' . $currentCategory->name . '"' : 'nào' }}
                    </p>
                    <p class="mb-6 text-sm font-medium text-gray-300">Hãy quay lại sau để khám phá các sự kiện mới.</p>
                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-2.5 text-xs font-bold text-black hover:bg-emerald-400 transition-colors shadow-lg shadow-emerald-500/20">
                        Xem tất cả sự kiện
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection