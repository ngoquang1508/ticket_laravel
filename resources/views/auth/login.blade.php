@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="flex min-h-[calc(100vh-64px)] items-center justify-center bg-background px-4 py-12">
    <div class="w-full max-w-md">

        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-border">

            {{-- Title --}}
            <div class="text-center">
                <h1 class="text-2xl font-bold text-foreground">
                    Đăng nhập
                </h1>

                <p class="mt-2 text-sm text-muted">
                    Đăng nhập để tiếp tục sử dụng Ticket System
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('login.authenticate') }}" method="POST" class="mt-8 space-y-5">
            @csrf
                {{-- Email --}}
                <div>
                    <label
                        for="email"
                        class="mb-2 block text-sm font-medium text-foreground"
                    >
                        Email
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Nhập email của bạn"
                        autocomplete="email"
                        class="w-full rounded-lg border border-border
                               bg-white px-4 py-3 text-sm text-foreground
                               outline-none transition
                               placeholder:text-subtle
                               focus:border-primary-600
                               focus:ring-2 focus:ring-primary-600/20"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm font-medium text-foreground"
                    >
                        Mật khẩu
                    </label>

                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Nhập mật khẩu"
                            autocomplete="current-password"
                            class="w-full rounded-lg border border-border
                                   bg-white px-4 py-3 pr-11 text-sm text-foreground
                                   outline-none transition
                                   placeholder:text-subtle
                                   focus:border-primary-600
                                   focus:ring-2 focus:ring-primary-600/20"
                        >

                        <button
                            id="toggle-password"
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-muted transition hover:text-primary-600"
                            aria-label="Hiện mật khẩu"
                        >
                            {{-- Eye --}}
                            <svg
                                id="eye-icon"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12
                                       18.25 18.75 12 18.75 2.25 12 2.25 12Z"
                                />
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="3"
                                    stroke-width="2"
                                />
                            </svg>

                            {{-- Eye off --}}
                            <svg
                                id="eye-off-icon"
                                xmlns="http://www.w3.org/2000/svg"
                                class="hidden h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3l18 18"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10.58 10.58a2 2 0 0 0 2.83 2.83"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9.88 5.08A10.94 10.94 0 0 1 12 4.75
                                       c6.25 0 9.75 7.25 9.75 7.25
                                       a18.3 18.3 0 0 1-3.03 3.98"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6.61 6.61C4.02 8.17 2.25 12 2.25 12
                                       s3.5 6.75 9.75 6.75
                                       a10.8 10.8 0 0 0 4.13-.8"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember + Forgot password --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex cursor-pointer items-center gap-2 text-muted">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-border text-primary-600
                                   focus:ring-primary-600"
                        >
                        Ghi nhớ đăng nhập
                    </label>

                    <a
                        href="#"
                        class="text-primary-600 transition hover:text-primary-700"
                    >
                        Quên mật khẩu?
                    </a>
                </div>

                {{-- Login button --}}
                <button
                    type="submit"
                    class="w-full rounded-lg bg-primary-600 px-4 py-3
                           font-semibold text-white transition
                           hover:bg-primary-700
                           focus:outline-none focus:ring-2
                           focus:ring-primary-600 focus:ring-offset-2"
                >
                    Đăng nhập
                </button>

            </form>

            {{-- Register --}}
            <p class="mt-6 text-center text-sm text-muted">
                Chưa có tài khoản?

                <a
                    href="{{ route('register') }}"
                    class="font-medium text-primary-600 transition
                           hover:text-primary-700"
                >
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
