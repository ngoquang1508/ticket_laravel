@extends('layouts.app')

@section('title', isset($currentCategory) ? $currentCategory->name . ' - Sự kiện' : 'Tất cả sự kiện')

@section('content')

    @include('components.event-category-nav')

    <div
        class="mx-auto max-w-7xl px-4 pt-8 {{ $pastEvents->isEmpty() && $upcomingEvents->isEmpty() ? 'pb-8' : '' }} sm:px-6 lg:px-8 text-white">

        {{-- Bộ lọc sự kiện phong cách Ticketbox (Lọc ngày, địa điểm, miễn phí, thể loại) --}}
        @include('events.partials.ticket-filter')

        {{-- Thanh thông báo từ khóa tìm kiếm --}}
        @if(!empty($searchTerm))
            <div
                class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md">
                <div class="flex items-center gap-2">
                    <span class="text-gray-400">Kết quả tìm kiếm cho:</span>
                    <span class="text-base font-bold text-emerald-400">"{{ $searchTerm }}"</span>
                    <span class="text-xs text-gray-400">({{ $upcomingEvents->total() + $pastEvents->total() }} sự kiện)</span>
                </div>
                <a href="{{ isset($currentCategory) ? route('events.index', $currentCategory->slug) : route('events.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-white transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Xóa bộ lọc tìm kiếm</span>
                </a>
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

        {{-- Trạng thái trống --}}
        @if($upcomingEvents->isEmpty() && $pastEvents->isEmpty())
            <div class="flex flex-col items-center justify-center text-center text-gray-400">
                @if(!empty($searchTerm))
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
                @else
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