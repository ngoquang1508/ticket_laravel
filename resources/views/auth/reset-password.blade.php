@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="flex min-h-[calc(100vh-64px)] items-center justify-center bg-background px-4 py-12">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm ring-1 ring-border">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-foreground">Đặt lại mật khẩu</h1>
            <p class="mt-2 text-sm text-muted">Nhập mật khẩu mới cho tài khoản của bạn.</p>
        </div>
        <form action="{{ route('password.update') }}" method="POST" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-foreground">Mật khẩu mới</label>
                <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Nhập mật khẩu mới" class="w-full rounded-lg border border-border bg-white px-4 py-3 text-sm text-foreground outline-none transition placeholder:text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
            </div>
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-foreground">Xác nhận mật khẩu</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới" class="w-full rounded-lg border border-border bg-white px-4 py-3 text-sm text-foreground outline-none transition placeholder:text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
            </div>
            <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-3 font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2">Đặt lại mật khẩu</button>
        </form>
    </div>
</div>
@endsection
