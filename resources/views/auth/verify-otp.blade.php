@extends('layouts.app')

@section('title', 'Xác thực OTP')

@section('content')
<div class="flex min-h-[calc(100vh-64px)] items-center justify-center bg-background px-4 py-12">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm ring-1 ring-border">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-foreground">Xác thực OTP</h1>
            <p class="mt-2 text-sm text-muted">Nhập mã OTP đã được gửi đến email của bạn.</p>
            <p class="mt-2 text-sm font-medium text-foreground">{{ $email }}</p>
        </div>
        <form action="{{ route('password.verify-otp') }}" method="POST" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="otp" class="mb-2 block text-sm font-medium text-foreground">Mã OTP</label>
                <input id="otp" type="text" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required autofocus autocomplete="one-time-code" placeholder="Nhập 6 chữ số" class="w-full rounded-lg border border-border bg-white px-4 py-3 text-center text-lg tracking-[0.4em] text-foreground outline-none transition placeholder:text-subtle placeholder:tracking-normal focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                <p class="mt-2 text-xs text-muted">OTP có hiệu lực trong 5 phút.</p>
            </div>
            <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-3 font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2">Xác nhận OTP</button>
        </form>
        <form action="{{ route('password.send-otp') }}" method="POST" class="mt-4 text-center">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="resend" value="1">
            <button id="resend-otp" type="submit" class="text-sm font-medium text-primary-600 hover:text-primary-700">Chưa nhận được mã? Gửi lại OTP</button>
        </form>
    </div>
</div>
<script>
    (() => {
        const button = document.getElementById('resend-otp');
        const email = @json($email);
        const lastSentAt = Number(localStorage.getItem(`password-reset-resend-${email}`) || 0);
        let seconds = 60 - Math.floor((Date.now() - lastSentAt) / 1000);
        if (!button || seconds <= 0) return;
        const enable = () => {
            button.disabled = false;
            button.textContent = 'Chưa nhận được mã? Gửi lại OTP';
            button.classList.remove('cursor-not-allowed', 'text-gray-400');
            button.classList.add('text-primary-600', 'hover:text-primary-700');
        };
        button.disabled = true;
        button.classList.add('cursor-not-allowed', 'text-gray-400');
        button.classList.remove('text-primary-600', 'hover:text-primary-700');
        button.textContent = `Gửi lại sau ${seconds}s`;
        const timer = setInterval(() => {
            seconds -= 1;
            if (seconds <= 0) {
                clearInterval(timer);
                enable();
            } else {
                button.textContent = `Gửi lại sau ${seconds}s`;
            }
        }, 1000);
    })();
</script>
@endsection
