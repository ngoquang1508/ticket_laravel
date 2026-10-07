@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý người dùng</h1>
                <p class="mt-1 text-sm text-gray-500">Danh sách người dùng trong hệ thống.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">+ Thêm người dùng</a>
        </div>

        <form method="GET" class="mb-6 grid gap-3 rounded-xl border bg-white p-4 md:grid-cols-5">
            <input name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, email, sđt..." class="rounded-lg border px-3 py-2 text-sm md:col-span-2">
            <select name="role" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">Tất cả vai trò</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                <option value="customer" @selected(request('role') === 'customer')>Customer</option>
            </select>
            <select name="is_active" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="1" @selected(request()->filled('is_active') && request('is_active') == '1')>Đang hoạt động</option>
                <option value="0" @selected(request()->filled('is_active') && request('is_active') == '0')>Đã khóa</option>
            </select>
            <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Lọc</button>
        </form>

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="border-b bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4">ID</th>
                            <th class="px-5 py-4">Họ tên</th>
                            <th class="px-5 py-4">Email</th>
                            <th class="px-5 py-4">Số điện thoại</th>
                            <th class="px-5 py-4">Vai trò</th>
                            <th class="px-5 py-4">Trạng thái</th>
                            <th class="px-5 py-4">Ngày tạo</th>
                            <th class="px-5 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">#{{ $user->id }}</td>
                                <td class="px-5 py-4 font-semibold text-gray-900">{{ $user->name }}</td>
                                <td class="px-5 py-4">{{ $user->email }}</td>
                                <td class="px-5 py-4">{{ $user->phone ?: '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="w-fit rounded-full px-2 py-1 text-xs {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="w-fit rounded-full px-2 py-1 text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-500">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-primary-700 hover:underline">Sửa</a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 hover:underline">Xóa</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">Không tìm thấy người dùng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    </div>
@endsection
