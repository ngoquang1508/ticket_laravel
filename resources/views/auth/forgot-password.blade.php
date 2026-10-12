@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
    <div class="flex min-h-[calc(100vh-64px)] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-2xl border border-white/10 bg-[#2b2b2b] p-6 sm:p-8 shadow-2xl">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Quên mật khẩu?</h1>
                <p class="mt-2 text-sm text-gray-400">Nhập email của bạn để nhận mã OTP đặt lại mật khẩu.</p>
            </div>
            <form action="{{ route('password.send-otp') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-200">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" placeholder="Nhập email của bạn"
                        class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 text-sm text-white outline-none transition placeholder:text-gray-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                </div>
                <button type="submit"
                    class="w-full rounded-xl bg-primary-600 px-4 py-3 font-semibold text-white transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-[#2b2b2b] shadow-lg shadow-primary-600/25 cursor-pointer">
                    Gửi mã OTP
                </button>
            </form>
            <a href="{{ route('login') }}"
                class="mt-6 block text-center text-sm font-medium text-primary-500 hover:text-primary-400 transition">
                ← Quay lại đăng nhập
            </a>
        </div>
    </div>
@endsection