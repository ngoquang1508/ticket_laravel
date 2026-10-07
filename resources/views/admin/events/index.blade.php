@extends('layouts.admin')

@section('title', 'Quản lý sự kiện')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý sự kiện</h1>
                <p class="mt-1 text-sm text-gray-500">Danh sách sự kiện trong hệ thống.</p>
            </div>
            <a href="{{ route('admin.events.create') }}"
                class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">+ Thêm sự
                kiện</a>
        </div>

        <form method="GET" class="mb-6 grid gap-3 rounded-xl border bg-white p-4 md:grid-cols-5">
            <input name="search" value="{{ request('search') }}" placeholder="Tìm theo tên..."
                class="rounded-lg border px-3 py-2 text-sm">
            <select name="location_id" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">Tất cả địa điểm</option>@foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}
                </option>@endforeach
            </select>
            <select name="sale_mode" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">Tất cả hình thức</option>
                <option value="general_admission" @selected(request('sale_mode') === 'general_admission')>Khu vực</option>
                <option value="assigned_seat" @selected(request('sale_mode') === 'assigned_seat')>Ghế</option>
                <option value="free_sale" @selected(request('sale_mode') === 'free_sale')>Tự do</option>
            </select>
            <select name="is_published" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="1" @selected(request('is_published') === '1')>Đã đăng</option>
                <option value="0" @selected(request('is_published') === '0')>Bản nháp</option>
            </select>
            <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Lọc</button>
        </form>

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="border-b bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4">Sự kiện</th>
                            <th class="px-5 py-4">Địa điểm</th>
                            <th class="px-5 py-4">Thời gian</th>
                            <th class="px-5 py-4">Hình thức</th>
                            <th class="px-5 py-4">Trạng thái</th>
                            <th class="px-5 py-4">Ticket/Seat</th>
                            <th class="px-5 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">@if($event->image)<img
                                        src="{{ asset('storage/' . $event->image) }}"
                                    class="h-12 w-16 rounded object-cover">@else<div
                                            class="h-12 w-16 rounded bg-gray-100"></div>@endif<div><a
                                                href="{{ route('admin.events.show', $event) }}"
                                                class="font-semibold text-gray-900 hover:text-primary-700">{{ $event->name }}</a>
                                            <p class="text-xs text-gray-500">{{ $event->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">{{ $event->location->name }}</td>
                                <td class="px-5 py-4 text-gray-600">
                                    {{ $event->starts_at->format('d/m/Y H:i') }}<br>{{ $event->ends_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4">
                                    {{ $event->sale_mode === 'assigned_seat' ? 'Ghế' : ($event->sale_mode === 'free_sale' ? 'Tự do' : 'Khu vực') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1"><span
                                            class="w-fit rounded-full px-2 py-1 text-xs {{ $event->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $event->is_published ? 'Đã đăng' : 'Bản nháp' }}</span>@if($event->is_featured)<span
                                                class="w-fit rounded-full bg-yellow-100 px-2 py-1 text-xs text-yellow-700">Nổi
                                            bật</span>@endif</div>
                                </td>
                                <td class="px-5 py-4">{{ $event->ticket_types_count }}/{{ $event->seats_count }}</td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('admin.events.show', $event) }}"
                                        class="font-medium text-primary-700 hover:underline">Xem</a><a
                                        href="{{ route('admin.events.edit', $event) }}"
                                        class="ml-3 font-medium text-primary-700 hover:underline">Sửa</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">Chưa có sự kiện nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $events->links() }}</div>
    </div>
@endsection