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
                                {{-- Cột thao tác --}}
                                <td class="px-6 py-4 text-right">
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
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Chưa có địa điểm nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $locations->links() }}</div>
</div>
@endsection
