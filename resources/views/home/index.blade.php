@extends('layouts.app')

@section('title', 'Trang chủ - Ticket System')

@section('content')

    {{-- Thanh điều hướng danh mục sự kiện --}}
    @include('components.event-category-nav')

    {{-- Hero Banner Slider đôi --}}
    @include('home.partials.hero-slider', ['events' => $heroEvents])

    {{-- Khung nội dung chính của trang chủ --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 space-y-10 sm:space-y-14">

        {{-- Sự kiện xu hướng --}}
        @include('home.partials.trending-events', ['events' => $trendingEvents])

        {{-- Sự kiện lọc theo Thời gian: Cuối tuần này / Tháng này --}}
        @include('home.partials.time-filter-events', [
            'weekendEvents' => $weekendEvents,
            'monthEvents' => $monthEvents
        ])

        {{-- Mục Vé bán lại --}}
        @include('home.partials.resale-events', ['events' => $resaleEvents])

        {{-- Khối sự kiện theo từng thể loại cụ thể (Thể thao, Âm nhạc...) --}}
        @include('home.partials.category-section', ['categories' => $categorySections])

        {{-- Điểm đến thú vị --}}
        @include('home.partials.destinations', ['destinations' => $destinations])

    </div>

@endsection