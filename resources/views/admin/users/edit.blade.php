@extends('layouts.admin')

@section('title', 'Cập nhật người dùng')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-900">&larr; Quay lại danh sách</a>
                <h1 class="mt-2 text-2xl font-bold text-gray-900">Cập nhật thông tin người dùng</h1>
            </div>
        </div>

        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @method('PUT')
                @include('admin.users._form')

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
