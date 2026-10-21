@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
    <div class="flex min-h-[calc(100vh-64px)] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="rounded-2xl border border-white/10 bg-[#2b2b2b] p-6 sm:p-8 shadow-2xl">

                {{-- Tiêu đề --}}
                <div class="text-center">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                        Tạo tài khoản
                    </h1>

                    <p class="mt-2 text-sm text-gray-400">
                        Đăng ký tài khoản để bắt đầu đặt vé
                    </p>
                </div>

                {{-- Form đăng ký --}}
                <form action="{{ route('register.store') }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    {{-- Họ và tên --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-200">
                            Họ và tên
                        </label>

                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nhập họ và tên"
                            autocomplete="name" required
                            class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 text-sm text-white
                                   outline-none transition placeholder:text-gray-500
                                   focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

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

                    {{-- Số điện thoại --}}
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-gray-200">
                            Số điện thoại
                        </label>

                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                            placeholder="Nhập số điện thoại" autocomplete="tel" required
                            class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 text-sm text-white
                                   outline-none transition placeholder:text-gray-500
                                   focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    {{-- Mật khẩu --}}
                    <div>
                        <label for="register-password" class="mb-2 block text-sm font-medium text-gray-200">
                            Mật khẩu
                        </label>

                        <div class="relative">
                            <input id="register-password" type="password" name="password" placeholder="Nhập mật khẩu"
                                autocomplete="new-password" required
                                class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 pr-11 text-sm text-white
                                       outline-none transition placeholder:text-gray-500
                                       focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">

                            <button id="toggle-register-password" type="button" class="absolute right-3 top-1/2
                                       -translate-y-1/2 text-gray-400 transition hover:text-white cursor-pointer"
                                aria-label="Hiện mật khẩu">
                                <x-icons.eye id="register-eye-icon" class="h-5 w-5" />

                                <x-icons.eye-off id="register-eye-off-icon" class="hidden h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    {{-- Xác nhận mật khẩu --}}
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-200">
                            Xác nhận mật khẩu
                        </label>

                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="Nhập lại mật khẩu" autocomplete="new-password" required
                                class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 pr-11 text-sm text-white
                                       outline-none transition placeholder:text-gray-500
                                       focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">

                            <button id="toggle-confirm-password" type="button" class="absolute right-3 top-1/2
                                       -translate-y-1/2 text-gray-400 transition hover:text-white cursor-pointer"
                                aria-label="Hiện mật khẩu">
                                <x-icons.eye id="confirm-eye-icon" class="h-5 w-5" />

                                <x-icons.eye-off id="confirm-eye-off-icon" class="hidden h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    {{-- Nút đăng ký --}}
                    <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3
                               font-semibold text-white transition hover:bg-primary-500
                               focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                               focus:ring-offset-[#2b2b2b] shadow-lg shadow-primary-600/25 cursor-pointer">
                        Đăng ký
                    </button>

                </form>

                {{-- Đăng nhập --}}
                <p class="mt-6 text-center text-sm text-gray-400">
                    Đã có tài khoản?
                    <a href="{{ route('login') }}" class="font-medium text-primary-500 transition hover:text-primary-400 ml-1">
                        Đăng nhập
                    </a>
                </p>

            </div>

        </div>
    </div>

    <script>
        function setupPasswordToggle(inputId, buttonId, eyeId, eyeOffId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            const eye = document.getElementById(eyeId);
            const eyeOff = document.getElementById(eyeOffId);

            button.addEventListener('click', () => {
                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';

                eye.classList.toggle('hidden', isPassword);
                eyeOff.classList.toggle('hidden', !isPassword);

                button.setAttribute(
                    'aria-label',
                    isPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu'
                );
            });
        }

        setupPasswordToggle(
            'register-password',
            'toggle-register-password',
            'register-eye-icon',
            'register-eye-off-icon'
        );

        setupPasswordToggle(
            'password_confirmation',
            'toggle-confirm-password',
            'confirm-eye-icon',
            'confirm-eye-off-icon'
        );
    </script>
@endsection