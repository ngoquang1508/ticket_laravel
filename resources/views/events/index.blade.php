@extends('layouts.app')

@section('title', isset($currentCategory) ? $currentCategory->name . ' - Sự kiện' : 'Tất cả sự kiện')

@section('content')

    @include('components.event-category-nav')

    <div
        class="mx-auto max-w-7xl px-4 pt-8 {{ $pastEvents->isEmpty() && $upcomingEvents->isEmpty() ?? 'pb-8' }} sm:px-6 lg:px-8 text-white">

        {{-- Sắp diễn ra --}}
        <x-event-section :events="$upcomingEvents" title="Sắp diễn ra" type="upcoming" :is-past="false"
            :category-slug="$currentCategory ? $currentCategory->slug : null" />

        @if($upcomingEvents->isNotEmpty() && $pastEvents->isNotEmpty())
            <div class="h-px w-full bg-gray-400/50"></div>
        @endif

        {{-- Đã diễn ra --}}
        <x-event-section :events="$pastEvents" title="Đã diễn ra" type="past" :is-past="true"
            :category-slug="$currentCategory ? $currentCategory->slug : null" />

        @if($upcomingEvents->isEmpty() && $pastEvents->isEmpty())
            <div class="py-12 text-center text-gray-400">
                Chưa có sự kiện nào.
            </div>
        @endif
    </div>
@endsection