{{--
Modal Thanh toán (Checkout Modal Partial)
Chỉ chứa HTML/CSS/JS của modal thanh toán, không chứa trang nền.
Sử dụng trực tiếp tại: Trang đặt vé (booking.show) và Trang vé của tôi (user.tickets).
--}}

<div id="checkout-modal-{{ $order->id }}" onclick="if(event.target === this) closeCheckoutModal({{ $order->id }})"
    class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm p-3 sm:p-4 flex items-center justify-center hidden transition-all duration-300 checkout-modal-backdrop"
    data-order-id="{{ $order->id }}" data-order-code="{{ $order->order_code }}"
    data-expires-at="{{ $order->expires_at->toIso8601String() }}">

    <!-- Container Modal (Giới hạn chiều cao max-h, header cố định và body cuộn mượt mà) -->
    <div id="checkout-modal-container-{{ $order->id }}"
        class="relative w-full max-w-lg my-auto rounded-2xl bg-[#222222] border border-white/10 text-white shadow-2xl flex flex-col max-h-[92vh] sm:max-h-[90vh] overflow-hidden animate-fade-in-up">

        <!-- Header Modal (Cố định ở trên cùng, luôn nhìn thấy nút X và tiêu đề) -->
        <div
            class="shrink-0 flex items-center justify-between border-b border-white/10 px-4 py-3 sm:px-6 sm:py-3.5 bg-[#222222] z-10">
            <div class="min-w-0 pr-2">
                <h2 class="text-base sm:text-lg font-bold text-white truncate">
                    Hoàn tất thanh toán
                </h2>
                <p class="text-gray-400 text-xs mt-0.5 line-clamp-1">
                    Ghế của bạn đang được giữ chỗ trong thời gian đếm ngược
                </p>
            </div>

            <!-- Nút đóng 'X' modal thuần túy -->
            <button type="button" onclick="closeCheckoutModal({{ $order->id }})"
                class="shrink-0 text-gray-400 hover:text-white hover:bg-white/10 p-1.5 sm:p-2 rounded-xl transition duration-150 flex items-center justify-center cursor-pointer"
                title="Đóng modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Thân Modal (Cuộn nội dung độc lập, thanh cuộn mượt mà) -->
        <div class="overflow-y-auto p-4 sm:p-5 space-y-3.5 sm:space-y-4 custom-scrollbar">
            <!-- Đồng hồ đếm ngược -->
            <div class="flex justify-center">
                <div id="countdown-wrapper-{{ $order->id }}"
                    class="w-full sm:w-auto bg-amber-500/10 border border-amber-500/20 rounded-xl px-4 py-2 sm:px-6 sm:py-2.5 text-center transition-all duration-300">
                    <span class="block text-[11px] font-semibold text-amber-400 uppercase tracking-wider mb-0.5">Thời
                        gian giữ vé còn lại</span>
                    <div class="text-2xl sm:text-3xl font-mono font-bold text-amber-400 tracking-wider countdown-display"
                        id="countdown-{{ $order->id }}">10:00</div>
                </div>
            </div>

            <!-- Thông tin đơn hàng tóm tắt -->
            <div class="bg-[#292929] rounded-xl p-3 sm:p-4 border border-white/10 text-xs sm:text-sm">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-gray-400">Mã đơn hàng:</span>
                    <span class="font-mono font-bold text-white tracking-wide">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-start mb-1.5 gap-2">
                    <span class="text-gray-400 shrink-0">Sự kiện:</span>
                    <span class="font-medium text-white text-right line-clamp-1">{{ $order->event->name }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-white/10 mt-1.5">
                    <span class="text-gray-400 font-medium">Tổng tiền:</span>
                    <span
                        class="text-base sm:text-lg font-semibold text-emerald-400">{{ number_format($order->total_amount, 0, ',', '.') }}
                        đ</span>
                </div>
            </div>

            <!-- Các hành động thanh toán (khi đơn còn hiệu lực) -->
            <div id="payment-actions-{{ $order->id }}">
                <form id="payment-form-{{ $order->id }}"
                    action="{{ route('orders.process-payment', $order->order_code) }}" method="POST">
                    @csrf
                    <h3 class="font-semibold text-white mb-2 text-xs sm:text-sm flex items-center gap-2">
                        <span>Chọn hình thức thanh toán:</span>
                    </h3>

                    @php
                        $canPayDirect = $order->event ? $order->event->canPayDirect() : false;
                    @endphp

                    <div class="space-y-2 mb-3.5 sm:mb-4">
                        <!-- Lựa chọn VNPay -->
                        <label
                            class="flex items-center justify-between p-2.5 sm:p-3 border border-white/10 rounded-xl cursor-pointer bg-[#292929] hover:bg-white/5 transition has-[:checked]:border-emerald-500/60 has-[:checked]:bg-emerald-500/10 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500/50">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <input type="radio" name="payment_method" value="vnpay"
                                    class="w-4 h-4 text-emerald-500 border-white/20 bg-black/40 focus:ring-emerald-500 shrink-0"
                                    checked>
                                <div class="min-w-0">
                                    <span class="block text-xs sm:text-sm font-semibold text-white truncate">Thanh toán
                                        qua VNPay</span>
                                    <span class="block text-[11px] text-gray-400 truncate">Quét mã QR, Thẻ ATM,
                                        Visa/Mastercard</span>
                                </div>
                            </div>
                        </label>

                        <!-- Lựa chọn Thanh toán trực tiếp -->
                        @if($canPayDirect)
                            <label
                                class="flex items-center justify-between p-2.5 sm:p-3 border border-white/10 rounded-xl cursor-pointer bg-[#292929] hover:bg-white/5 transition has-[:checked]:border-emerald-500/60 has-[:checked]:bg-emerald-500/10 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500/50">
                                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                    <input type="radio" name="payment_method" value="direct"
                                        class="w-4 h-4 text-emerald-500 border-white/20 bg-black/40 focus:ring-emerald-500 shrink-0">
                                    <div class="min-w-0">
                                        <span class="block text-xs sm:text-sm font-semibold text-white truncate">Thanh toán
                                            trực tiếp</span>
                                        <span class="block text-[11px] text-gray-400 truncate">Chuyển khoản thủ công hoặc
                                            tại quầy</span>
                                    </div>
                                </div>
                            </label>
                        @else
                            <div
                                class="flex items-center justify-between p-2.5 sm:p-3 border border-white/5 rounded-xl bg-[#202020] opacity-50 cursor-not-allowed">
                                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-gray-600 border-white/10 bg-black/20 shrink-0">
                                    <div class="min-w-0">
                                        <span class="block text-xs sm:text-sm font-semibold text-gray-400 truncate">Thanh
                                            toán trực tiếp</span>
                                        <span class="block text-[11px] text-red-400 truncate">Đã ngừng nhận (sự kiện diễn ra
                                            trong vòng 48h)</span>
                                    </div>
                                </div>
                                <span
                                    class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20">Đã
                                    đóng</span>
                            </div>
                        @endif
                    </div>

                    <!-- Nút xác nhận thanh toán -->
                    <button type="submit" id="btn-pay-{{ $order->id }}"
                        class="w-full bg-emerald-500 text-white font-bold py-2.5 sm:py-3 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/20 text-xs sm:text-sm cursor-pointer flex items-center justify-center gap-2">
                        <span>Tiến hành thanh toán</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <!-- Nút thao tác phụ: Đóng modal và Hủy đơn hàng -->
                <div class="mt-2.5 sm:mt-3 grid grid-cols-2 gap-2">
                    <button type="button" onclick="closeCheckoutModal({{ $order->id }})"
                        class="text-center bg-white/5 text-gray-300 border border-white/10 font-semibold py-2 sm:py-2.5 px-2 rounded-xl hover:bg-white/10 hover:text-white transition text-xs flex items-center justify-center truncate cursor-pointer">
                        Đóng modal (Giữ vé)
                    </button>

                    <form id="cancel-form-{{ $order->id }}" action="{{ route('orders.cancel', $order->order_code) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Ghế đang giữ sẽ được giải phóng ngay lập tức.');">
                        @csrf
                        <button type="submit" id="btn-cancel-{{ $order->id }}"
                            class="w-full bg-white/5 text-gray-400 border border-white/10 font-semibold py-2 sm:py-2.5 px-2 rounded-xl hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 transition text-xs cursor-pointer truncate">
                            Hủy đơn hàng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hành động khi thời gian giữ vé đã hết (Ẩn mặc định) -->
            <div id="expired-actions-{{ $order->id }}" class="hidden">
                <div class="text-center py-3 space-y-2.5">
                    <p class="text-xs sm:text-sm text-red-400">Đơn hàng đã hết hạn giữ vé và các ghế đã được mở lại cho
                        người khác.</p>
                    <a href="{{ route('events.book', $order->event->slug) }}"
                        class="block w-full text-center bg-emerald-500 text-white font-bold py-2.5 sm:py-3 rounded-xl hover:bg-emerald-600 transition text-xs sm:text-sm">
                        Đặt lại vé ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script đếm ngược riêng cho từng đơn hàng --}}
<script>
    (function () {
        const orderId = "{{ $order->id }}";
        const expiresAt = new Date("{{ $order->expires_at->toIso8601String() }}").getTime();
        const countdownEl = document.getElementById('countdown-' + orderId);
        const countdownWrapper = document.getElementById('countdown-wrapper-' + orderId);
        const paymentActions = document.getElementById('payment-actions-' + orderId);
        const expiredActions = document.getElementById('expired-actions-' + orderId);
        let timer = null;

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance <= 0) {
                if (countdownEl) countdownEl.innerHTML = "00:00";
                if (countdownWrapper) {
                    countdownWrapper.classList.remove('bg-amber-500/10', 'border-amber-500/20');
                    countdownWrapper.classList.add('bg-red-500/10', 'border-red-500/20');
                }
                if (countdownEl) {
                    countdownEl.classList.remove('text-amber-400');
                    countdownEl.classList.add('text-red-400');
                }

                // Ẩn form thanh toán, hiển thị hành động khi hết hạn
                if (paymentActions) paymentActions.classList.add('hidden');
                if (expiredActions) expiredActions.classList.remove('hidden');

                clearInterval(timer);

                // Gửi request hủy đơn và giải phóng ghế trong nền
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

            const timeStr = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
            if (countdownEl) countdownEl.innerHTML = timeStr;
        }

        timer = setInterval(updateCountdown, 1000);
        updateCountdown();
    })();
</script>

{{-- Khối Style và Script điều khiển chung (chỉ nạp 1 lần duy nhất) --}}
@once
    <style>
        /* Hiệu ứng hiển thị modal mượt mà */
        .animate-fade-in-up {
            animation: fadeInUpModal 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUpModal {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Thanh cuộn tinh tế cho modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }
    </style>

    <script>
        // Hàm mở modal thanh toán
        window.openCheckoutModal = function (orderId) {
            let modal = null;
            if (orderId) {
                modal = document.getElementById('checkout-modal-' + orderId)
                    || document.querySelector(`[data-order-code="${orderId}"]`);
            } else {
                modal = document.querySelector('.checkout-modal-backdrop');
            }

            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        };

        // Hàm đóng modal thanh toán
        window.closeCheckoutModal = function (orderId) {
            if (orderId) {
                const modal = document.getElementById('checkout-modal-' + orderId)
                    || document.querySelector(`[data-order-code="${orderId}"]`);
                if (modal) modal.classList.add('hidden');
            } else {
                document.querySelectorAll('.checkout-modal-backdrop').forEach(m => m.classList.add('hidden'));
            }
            document.body.classList.remove('overflow-hidden');
        };

        // Đóng modal khi nhấn phím Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.closeCheckoutModal();
            }
        });
    </script>
@endonce