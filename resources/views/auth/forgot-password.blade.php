@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="flex min-h-[calc(100vh-64px)] items-center justify-center bg-background px-4 py-12">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm ring-1 ring-border">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-foreground">Quên mật khẩu?</h1>
            <p class="mt-2 text-sm text-muted">Nhập email của bạn để nhận mã OTP đặt lại mật khẩu.</p>
        </div>
        <form action="{{ route('password.send-otp') }}" method="POST" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-foreground">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="Nhập email của bạn" class="w-full rounded-lg border border-border bg-white px-4 py-3 text-sm text-foreground outline-none transition placeholder:text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
            </div>
            <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-3 font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2">Gửi mã OTP</button>
        </form>
        <a href="{{ route('login') }}" class="mt-6 block text-center text-sm font-medium text-primary-600 hover:text-primary-700">← Quay lại đăng nhập</a>
    </div>
</div>
@endsection
