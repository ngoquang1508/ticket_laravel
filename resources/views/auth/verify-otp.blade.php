@extends('layouts.app')

@section('title', 'Xác thực OTP')

@section('content')
    <div class="flex min-h-[calc(100vh-64px)] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-2xl border border-white/10 bg-[#2b2b2b] p-6 sm:p-8 shadow-2xl">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Xác thực OTP</h1>
                <p class="mt-2 text-sm text-gray-400">Nhập mã OTP đã được gửi đến email của bạn.</p>
                <p class="mt-2 text-sm font-semibold text-primary-400 bg-primary-500/10 border border-primary-500/20 py-1 px-3 rounded-lg inline-block">{{ $email }}</p>
            </div>
            <form action="{{ route('password.verify-otp') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="otp" class="mb-2 block text-sm font-medium text-gray-200">Mã OTP</label>
                    <input id="otp" type="text" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required
                        autofocus autocomplete="one-time-code" placeholder="Nhập 6 chữ số"
                        class="w-full rounded-xl border border-white/10 bg-[#1e1e1e] px-4 py-3 text-center text-xl tracking-[0.4em] font-mono font-bold text-white outline-none transition placeholder:text-gray-500 placeholder:tracking-normal placeholder:font-normal placeholder:text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    <p class="mt-2 text-xs text-gray-400 text-center">OTP có hiệu lực trong 5 phút.</p>
                </div>
                <button type="submit"
                    class="w-full rounded-xl bg-primary-600 px-4 py-3 font-semibold text-white transition hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-[#2b2b2b] shadow-lg shadow-primary-600/25 cursor-pointer">
                    Xác nhận OTP
                </button>
            </form>
            <form action="{{ route('password.send-otp') }}" method="POST" class="mt-4 text-center">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="resend" value="1">
                <button id="resend-otp" type="submit"
                    class="text-sm font-medium text-primary-500 hover:text-primary-400 transition cursor-pointer">
                    Chưa nhận được mã? Gửi lại OTP
                </button>
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
                button.classList.remove('cursor-not-allowed', 'text-gray-500');
                button.classList.add('text-primary-500', 'hover:text-primary-400');
            };
            button.disabled = true;
            button.classList.add('cursor-not-allowed', 'text-gray-500');
            button.classList.remove('text-primary-500', 'hover:text-primary-400');
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