@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý danh mục</h1>
            <p class="mt-1 text-sm text-gray-500">Phân loại các sự kiện trong hệ thống.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">+ Thêm danh mục</a>
    </div>

    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b bg-gray-50 text-gray-600"><tr>
                    <th class="px-6 py-4 font-semibold">Tên</th>
                    <th class="px-6 py-4 font-semibold">Slug</th>
                    <th class="px-6 py-4 font-semibold">Trạng thái</th>
                    <th class="px-6 py-4 font-semibold">Số event</th>
                    <th class="px-6 py-4 text-right font-semibold">Thao tác</th>
                </tr></thead>
                <tbody class="divide-y">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-2 py-1 text-xs {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $category->is_active ? 'Đang hoạt động' : 'Tắt' }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $category->events_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="font-medium text-primary-700 hover:underline">Sửa</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                    @csrf @method('DELETE')
                                    <button class="font-medium text-red-600 hover:underline">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có danh mục nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $categories->links() }}</div>
</div>
@endsection
