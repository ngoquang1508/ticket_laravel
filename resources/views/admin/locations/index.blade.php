@extends('layouts.admin')

@section('title', 'Quản lý địa điểm')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Tiêu đề và nút thêm địa điểm --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Quản lý địa điểm</h1>
                <p class="mt-1 text-sm text-gray-500">Danh sách các địa điểm tổ chức sự kiện trong hệ thống.</p>
            </div>
            <div>
                <a href="{{ route('admin.locations.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Thêm địa điểm
                </a>
            </div>
        </div>

        {{-- Bộ lọc địa điểm --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
            <form method="GET" action="{{ route('admin.locations.index') }}"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12 items-center">
                {{-- Tìm kiếm tên hoặc địa chỉ --}}
                <div class="relative lg:col-span-9">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm kiếm theo tên địa điểm, địa chỉ..."
                        class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Nút bấm Lọc & Đặt lại --}}
                <div class="flex items-center gap-2 lg:col-span-3">
                    <button type="submit"
                        class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 cursor-pointer">
                        Lọc
                    </button>
                    @if (request()->filled('search'))
                        <a href="{{ route('admin.locations.index') }}" title="Xóa bộ lọc"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Bảng danh sách địa điểm --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead
                        class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-4 min-w-[240px]">Tên địa điểm</th>
                            <th class="px-5 py-4 min-w-[280px]">Địa chỉ</th>
                            <th class="px-5 py-4 min-w-[140px]">Số sự kiện</th>
                            <th class="px-5 py-4 min-w-[100px] text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($locations as $location)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-gray-900">{{ $location->name }}</span>
                                    @if ($location->description)
                                        <p class="mt-0.5 text-xs text-gray-500 line-clamp-1">{{ $location->description }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-600">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        <span>{{ $location->address }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                        {{ $location->events_count }} sự kiện
                                    </span>
                                </td>
                                {{-- Cột thao tác --}}
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Nút sửa --}}
                                        <a href="{{ route('admin.locations.edit', $location) }}"
                                            class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:text-primary-600 shadow-2xs"
                                            title="Chỉnh sửa địa điểm">
                                            <x-icons.pencil />
                                        </a>

                                        {{-- Nút xóa --}}
                                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa địa điểm {{ $location->name }}?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-100 hover:border-red-300 cursor-pointer shadow-2xs"
                                                title="Xóa địa điểm">
                                                <x-icons.delete />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-medium text-gray-900">Không tìm thấy địa điểm nào</p>
                                    <p class="mt-1 text-xs text-gray-500">Thử thay đổi từ khóa tìm kiếm của bạn.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $locations->links() }}</div>
    </div>
@endsection
