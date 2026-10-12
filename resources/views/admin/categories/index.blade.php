@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý danh mục</h1>
                <p class="mt-1 text-sm text-gray-500">Phân loại các sự kiện trong hệ thống.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}"
                class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">+ Thêm
                danh mục</a>
        </div>

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tên</th>
                            <th class="px-6 py-4 font-semibold">Slug</th>
                            <th class="px-6 py-4 font-semibold">Trạng thái</th>
                            <th class="px-6 py-4 font-semibold">Số event</th>
                            <th class="px-6 py-4 text-right font-semibold">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $category->slug }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $category->is_active ? 'Đang hoạt động' : 'Tắt' }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $category->events_count }}</td>
                                {{-- Cột thao tác --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Nút sửa --}}
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:text-primary-600 shadow-2xs"
                                            title="Chỉnh sửa danh mục">
                                            <x-icons.pencil />
                                        </a>

                                        {{-- Nút xóa --}}
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-100 hover:border-red-300 cursor-pointer shadow-2xs"
                                                title="Xóa danh mục">
                                                <x-icons.delete />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $categories->links() }}</div>
    </div>
@endsection