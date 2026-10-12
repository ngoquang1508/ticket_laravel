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
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12
                                                       18.25 18.75 12 18.75 2.25 12 2.25 12Z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                                </svg>

                                {{-- Icon ẩn mật khẩu --}}
                                <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.58 10.58a2 2 0 0 0 2.83 2.83" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.08A10.94 10.94 0 0 1 12 4.75
                                                       c6.25 0 9.75 7.25 9.75 7.25
                                                       a18.3 18.3 0 0 1-3.03 3.98" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.61 6.61C4.02 8.17 2.25 12 2.25 12
                                                       s3.5 6.75 9.75 6.75
                                                       a10.8 10.8 0 0 0 4.13-.8" />
                                </svg>
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