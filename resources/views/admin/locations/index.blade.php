@extends('layouts.admin')

@section('title', 'Quản lý địa điểm')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý địa điểm</h1>
            <p class="mt-1 text-sm text-gray-500">Danh sách địa điểm tổ chức sự kiện.</p>
        </div>
        <a href="{{ route('admin.locations.create') }}" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">
            + Thêm địa điểm
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Tên</th>
                        <th class="px-6 py-4 font-semibold">Địa chỉ</th>
                        <th class="px-6 py-4 font-semibold">Số event</th>
                        <th class="px-6 py-4 text-right font-semibold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($locations as $location)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $location->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $location->address }}</td>
                            <td class="px-6 py-4">{{ $location->events_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.locations.edit', $location) }}" class="font-medium text-primary-700 hover:underline">Sửa</a>
                                <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Bạn có chắc muốn xóa địa điểm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Chưa có địa điểm nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $locations->links() }}</div>
</div>
@endsection
