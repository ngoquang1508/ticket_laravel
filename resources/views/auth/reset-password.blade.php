@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <div class="flex min-h-[calc(100vh-64px)] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-2xl border border-white/10 bg-[#2b2b2b] p-6 sm:p-8 shadow-2xl">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Đặt lại mật khẩu</h1>
                <p class="mt-2 text-sm text-gray-400">Nhập mật khẩu mới cho tài khoản của bạn.</p>
            </div>
            <form action="{{ route('password.update') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-200">Mật khẩu mới</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                        placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)"
                        class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-200">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                        autocomplete="new-password" placeholder="Nhập lại mật khẩu mới"
                        class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                </div>
                <button type="submit"
                    class="w-full rounded-xl bg-primary-600 px-4 py-3 font-semibold text-white transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-[#2b2b2b] shadow-lg shadow-primary-600/25 cursor-pointer">
                    Đặt lại mật khẩu
                </button>
            </form>
            <a href="{{ route('login') }}"
                class="mt-6 block text-center text-sm font-medium text-primary-500 hover:text-primary-400 transition">
                ← Quay lại đăng nhập
            </a>
        </div>
    </div>
@endsection