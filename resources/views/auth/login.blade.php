@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
    <div class="flex min-h-[calc(100vh-64px)] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="rounded-2xl border border-white/10 bg-[#2b2b2b] p-6 sm:p-8 shadow-2xl">

                {{-- Tiêu đề --}}
                <div class="text-center">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                        Đăng nhập
                    </h1>

                    <p class="mt-2 text-sm text-gray-400">
                        Đăng nhập để tiếp tục sử dụng Ticket System
                    </p>
                </div>

                {{-- Form đăng nhập --}}
                <form action="{{ route('login.authenticate') }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-200">
                            Email
                        </label>

                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="Nhập email của bạn" autocomplete="email" required
                            class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 text-sm text-white
                                   outline-none transition placeholder:text-gray-500
                                   focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    {{-- Mật khẩu --}}
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-gray-200">
                            Mật khẩu
                        </label>

                        <div class="relative">
                            <input id="password" type="password" name="password" placeholder="Nhập mật khẩu"
                                autocomplete="current-password" required
                                class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 pr-11 text-sm text-white
                                       outline-none transition placeholder:text-gray-500
                                       focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">

                            <button id="toggle-password" type="button" class="absolute right-3 top-1/2 -translate-y-1/2
                                                   text-gray-400 transition hover:text-white cursor-pointer"
                                aria-label="Hiện mật khẩu">
                                {{-- Icon hiện mật khẩu --}}
                                <x-icons.eye id="eye-icon" class="h-5 w-5" />

                                {{-- Icon ẩn mật khẩu --}}
                                <x-icons.eye-off id="eye-off-icon" class="hidden h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    {{-- Ghi nhớ đăng nhập + Quên mật khẩu --}}
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex cursor-pointer items-center gap-2 text-gray-300 hover:text-white transition">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/20 bg-[#1e1e1e] text-primary-500
                                                   focus:ring-primary-500 focus:ring-offset-[#2b2b2b]">
                            <span>Ghi nhớ đăng nhập</span>
                        </label>

                        <a href="{{ route('password.request') }}"
                            class="text-primary-500 transition hover:text-primary-400 font-medium">
                            Quên mật khẩu?
                        </a>
                    </div>

                    {{-- Nút đăng nhập --}}
                    <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3
                                           font-semibold text-white transition hover:bg-primary-500
                                           focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                           focus:ring-offset-[#2b2b2b] shadow-lg shadow-primary-600/25 cursor-pointer">
                        Đăng nhập
                    </button>

                </form>

                {{-- Đăng ký tài khoản --}}
                <p class="mt-6 text-center text-sm text-gray-400">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="font-medium text-primary-500 transition hover:text-primary-400 ml-1">
                        Đăng ký ngay
                    </a>
                </p>

            </div>

        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeOffIcon = document.getElementById('eye-off-icon');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            eyeIcon.classList.toggle('hidden', isPassword);
            eyeOffIcon.classList.toggle('hidden', !isPassword);

            togglePassword.setAttribute(
                'aria-label',
                isPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu'
            );
        });
    </script>
@endsection