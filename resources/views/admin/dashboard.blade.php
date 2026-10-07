@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_description', 'Tổng quan hoạt động hệ thống')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dashboard</h1><p class="mt-1 text-sm text-gray-500">Tổng quan dữ liệu quản trị.</p></div>
        <a href="{{ route('admin.events.create') }}" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">+ Tạo sự kiện</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([['Sự kiện', $eventCount, 'admin.events.index'], ['Đã đăng', $publishedEventCount, 'admin.events.index'], ['Địa điểm', $locationCount, 'admin.locations.index'], ['Danh mục', $categoryCount, 'admin.categories.index'], ['Loại vé', $ticketTypeCount, 'admin.events.index']] as [$label, $value, $route])
            <a href="{{ route($route) }}" class="rounded-xl border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"><p class="text-sm text-gray-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p><p class="mt-2 text-xs font-medium text-primary-700">Xem chi tiết →</p></a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <section class="overflow-hidden rounded-xl border bg-white shadow-sm"><div class="flex items-center justify-between border-b px-5 py-4"><h2 class="font-semibold">Sự kiện gần đây</h2><a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-primary-700 hover:underline">Tất cả</a></div><div class="divide-y">@forelse($recentEvents as $event)<a href="{{ route('admin.events.show', $event) }}" class="flex items-center justify-between gap-4 px-5 py-4 hover:bg-gray-50"><div><p class="font-medium text-gray-900">{{ $event->name }}</p><p class="mt-1 text-xs text-gray-500">{{ $event->location->name }} · {{ $event->starts_at->format('d/m/Y H:i') }}</p></div><span class="rounded-full px-2 py-1 text-xs {{ $event->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $event->is_published ? 'Đã đăng' : 'Bản nháp' }}</span></a>@empty<p class="px-5 py-12 text-center text-sm text-gray-500">Chưa có sự kiện nào.</p>@endforelse</div></section>
        <section class="rounded-xl border bg-white p-5 shadow-sm"><h2 class="font-semibold">Thao tác nhanh</h2><div class="mt-4 space-y-2"><a href="{{ route('admin.locations.create') }}" class="block rounded-lg border px-4 py-3 text-sm font-medium hover:bg-gray-50">+ Thêm địa điểm</a><a href="{{ route('admin.categories.create') }}" class="block rounded-lg border px-4 py-3 text-sm font-medium hover:bg-gray-50">+ Thêm danh mục</a><a href="{{ route('admin.events.create') }}" class="block rounded-lg border px-4 py-3 text-sm font-medium hover:bg-gray-50">+ Thêm sự kiện</a></div></section>
    </div>
</div>
@endsection
