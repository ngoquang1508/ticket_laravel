@extends('layouts.app')
@section('title', 'Thanh toán đơn hàng')

@section('content')
<!-- Dark overlay background to simulate modal -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <!-- Modal Container -->
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col relative animate-fade-in-up">
        
        <!-- Header -->
        <div class="bg-gray-50 border-b border-gray-100 p-4 text-center relative">
            <h2 class="text-xl font-bold text-gray-900">Hoàn tất thanh toán</h2>
            <p class="text-gray-500 text-xs mt-1">Ghế của bạn đang được giữ trong thời gian đếm ngược</p>
        </div>

        <!-- Body -->
        <div class="p-5">
            <!-- Countdown -->
            <div class="flex justify-center mb-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-5 py-2 text-center">
                    <span class="block text-[11px] font-semibold text-yellow-600 uppercase tracking-wide mb-0.5">Thời gian giữ vé còn lại</span>
                    <div class="text-2xl font-mono font-bold text-yellow-600" id="countdown">10:00</div>
                </div>
            </div>

            <!-- Order Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-5 border border-gray-100 text-sm">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-gray-500">Mã đơn:</span>
                    <span class="font-semibold text-gray-900">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-gray-500">Sự kiện:</span>
                    <span class="font-semibold text-gray-900 line-clamp-1 text-right max-w-[60%]">{{ $order->event->name }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 mt-2">
                    <span class="text-gray-500 font-medium">Tổng tiền:</span>
                    <span class="text-lg font-bold text-primary-600">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                </div>
            </div>

            <div id="payment-actions">
                <form id="payment-form" action="{{ route('orders.process-payment', $order->order_code) }}" method="POST">
                    @csrf
                    <h3 class="font-semibold text-gray-900 mb-2 text-sm">Chọn phương thức thanh toán:</h3>
                    
                    <div class="space-y-2 mb-5">
                        <!-- VNPay Option -->
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition peer-checked:border-primary-500 peer-checked:bg-primary-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 has-[:checked]:ring-1 has-[:checked]:ring-primary-500">
                            <input type="radio" name="payment_method" value="vnpay" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500" checked>
                            <span class="ml-3 text-sm font-medium text-gray-900">Thanh toán qua VNPay</span>
                        </label>

                        <!-- Direct Option -->
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition peer-checked:border-primary-500 peer-checked:bg-primary-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 has-[:checked]:ring-1 has-[:checked]:ring-primary-500">
                            <input type="radio" name="payment_method" value="direct" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Thanh toán trực tiếp</span>
                        </label>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" id="btn-pay" class="w-full bg-primary-600 text-white font-bold py-2.5 rounded-lg hover:bg-primary-700 transition shadow-sm text-sm">
                            Tiến hành thanh toán
                        </button>
                    </div>
                </form>
                
                <form id="cancel-form" action="{{ route('orders.cancel', $order->order_code) }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" id="btn-cancel" class="w-full bg-white text-gray-500 border border-gray-200 font-semibold py-2.5 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition text-sm">
                        Hủy đơn hàng
                    </button>
                </form>
            </div>

            <!-- Expired Actions (Hidden by default) -->
            <div id="expired-actions" class="hidden">
                <a href="{{ route('events.book', $order->event->slug) }}" class="block w-full text-center bg-gray-200 text-gray-800 font-bold py-3 rounded-lg hover:bg-gray-300 transition text-sm">
                    Đóng
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const expiresAt = new Date("{{ $order->expires_at->toIso8601String() }}").getTime();
        const countdownEl = document.getElementById('countdown');
        const paymentActions = document.getElementById('payment-actions');
        const expiredActions = document.getElementById('expired-actions');
        let timer = null;

        function showToast() {
            if (window.showAppToast) {
                window.showAppToast('error', 'Có lỗi xảy ra', 'Thời gian giữ vé đã hết!');
            } else {
                // Fallback in case JS hasn't fully loaded
                alert('Thời gian giữ vé đã hết!');
            }
        }

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance <= 0) {
                countdownEl.innerHTML = "00:00";
                countdownEl.parentElement.classList.remove('bg-yellow-50', 'border-yellow-200');
                countdownEl.parentElement.classList.add('bg-red-50', 'border-red-200');
                countdownEl.classList.remove('text-yellow-600');
                countdownEl.classList.add('text-red-600');
                
                // Hide payment and cancel buttons, show Close button
                paymentActions.classList.add('hidden');
                expiredActions.classList.remove('hidden');

                showToast();
                clearInterval(timer);

                // Send background request to release seats immediately
                fetch("{{ route('orders.cancel', $order->order_code) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.innerHTML = 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
        }

        timer = setInterval(updateCountdown, 1000);
        updateCountdown();
    });
</script>
@endsection
