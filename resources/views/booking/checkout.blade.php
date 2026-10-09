@extends('layouts.app')
@section('title', 'Thanh toán đơn hàng')

@section('content')
<div class="min-h-screen pt-12 pb-24 text-white">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-[#1e1e1e] rounded-2xl p-8 border border-gray-800 shadow-xl text-center">
            <h2 class="text-2xl font-bold text-green-500 mb-2">Đang giữ vé!</h2>
            <p class="text-gray-400 mb-6">Vui lòng hoàn tất thanh toán trước khi thời gian giữ vé kết thúc.</p>
            
            <div class="text-4xl font-mono font-bold text-yellow-500 mb-8" id="countdown">
                --:--
            </div>

            <div class="bg-[#2b2b2b] p-6 rounded-xl border border-gray-700 text-left mb-8">
                <h3 class="font-bold text-lg mb-4">Thông tin đơn hàng</h3>
                <div class="space-y-2 text-gray-300">
                    <p>Mã đơn: <span class="text-white font-semibold">{{ $order->order_code }}</span></p>
                    <p>Sự kiện: <span class="text-white font-semibold">{{ $order->event->name }}</span></p>
                    <p>Tổng tiền: <span class="text-green-500 font-bold">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span></p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <form action="{{ route('orders.confirm', $order->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold transition duration-300 bg-[#1db954] text-white hover:bg-white hover:text-black">
                        Xác nhận Thanh toán
                    </button>
                </form>

                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold transition duration-300 bg-red-500 text-white hover:bg-red-400">
                        Hủy đơn & Trả vé
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const expiresAt = new Date("{{ $order->expires_at->toIso8601String() }}").getTime();
        const countdownEl = document.getElementById('countdown');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                countdownEl.innerHTML = "ĐÃ HẾT HẠN";
                countdownEl.classList.remove('text-yellow-500');
                countdownEl.classList.add('text-red-500');
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.innerHTML = 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    });
</script>
@endsection
