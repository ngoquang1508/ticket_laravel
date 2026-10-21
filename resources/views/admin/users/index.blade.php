@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Tiêu đề và nút thêm người dùng --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Quản lý người dùng</h1>
                <p class="mt-1 text-sm text-gray-500">Danh sách người dùng và phân quyền tài khoản trong hệ thống.</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <x-icons.plus class="h-4 w-4" />
                    Thêm người dùng
                </a>
            </div>
        </div>

        {{-- Bộ lọc người dùng --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
            <form method="GET" action="{{ route('admin.users.index') }}"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12 items-center">
                {{-- Tìm kiếm tên, email, sđt --}}
                <div class="relative lg:col-span-5">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <x-icons.search class="h-4 w-4" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm theo tên, email, SĐT..."
                        class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Lọc vai trò --}}
                <div class="lg:col-span-2">
                    <select name="role"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="customer" @selected(request('role') === 'customer')>Customer</option>
                    </select>
                </div>

                {{-- Lọc trạng thái --}}
                <div class="lg:col-span-2">
                    <select name="is_active"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" @selected(request()->filled('is_active') && request('is_active') == '1')>Đang hoạt động</option>
                        <option value="0" @selected(request()->filled('is_active') && request('is_active') == '0')>Đã khóa</option>
                    </select>
                </div>

                {{-- Nút bấm Lọc & Đặt lại --}}
                <div class="flex items-center gap-2 lg:col-span-3">
                    <button type="submit"
                        class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 cursor-pointer">
                        Lọc
                    </button>
                    @if (request()->anyFilled(['search', 'role', 'is_active']))
                        <a href="{{ route('admin.users.index') }}" title="Xóa bộ lọc"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                            <x-icons.close class="h-4 w-4 text-gray-500" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Bảng danh sách người dùng --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[880px] text-left text-sm">
                    <thead
                        class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-4 min-w-[60px]">ID</th>
                            <th class="px-5 py-4 min-w-[200px]">Họ tên</th>
                            <th class="px-5 py-4 min-w-[200px]">Email</th>
                            <th class="px-5 py-4 min-w-[130px]">Số điện thoại</th>
                            <th class="px-5 py-4 min-w-[120px]">Vai trò</th>
                            <th class="px-5 py-4 min-w-[140px]">Trạng thái</th>
                            <th class="px-5 py-4 min-w-[150px]">Ngày tạo</th>
                            <th class="px-5 py-4 min-w-[100px] text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-5 py-4 font-mono text-xs text-gray-400">#{{ $user->id }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-xs font-bold text-primary-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-600">{{ $user->email }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $user->phone ?: '-' }}</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $user->role === 'admin' ? 'border border-red-200 bg-red-50 text-red-700' : 'border border-blue-200 bg-blue-50 text-blue-700' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Hoạt động
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Đã khóa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                {{-- Cột thao tác --}}
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Nút sửa --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:text-primary-600 shadow-2xs"
                                            title="Chỉnh sửa người dùng">
                                            <x-icons.pencil />
                                        </a>

                                        {{-- Nút xóa (chỉ hiển thị khi không phải tài khoản hiện tại) --}}
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng {{ $user->name }} không?');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-100 hover:border-red-300 cursor-pointer shadow-2xs"
                                                    title="Xóa người dùng">
                                                    <x-icons.delete />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-3">
                                        <x-icons.user class="h-6 w-6" />
                                    </div>
                                    <p class="font-medium text-gray-900">Không tìm thấy người dùng nào</p>
                                    <p class="mt-1 text-xs text-gray-500">Thử thay đổi bộ lọc tìm kiếm hoặc từ khóa tra cứu.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    </div>
@endsection
