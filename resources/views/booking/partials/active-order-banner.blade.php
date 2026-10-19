{{-- Banner thông báo khi người dùng đang có đơn hàng giữ vé chưa thanh toán --}}
@if(isset($activeOrder) && $activeOrder && $activeOrder->expires_at > now())
    <div class="mb-5 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 sm:p-5 text-amber-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center shrink-0 text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-white text-sm sm:text-base">
                    Bạn đang có đơn hàng giữ vé #{{ $activeOrder->order_code }}
                </p>
                <p class="text-xs sm:text-sm text-amber-300/80 mt-0.5">
                    Số lượng: {{ $activeOrder->tickets->count() }} vé | Tổng tiền: {{ number_format($activeOrder->total_amount, 0, ',', '.') }} đ
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
            <button type="button" onclick="openCheckoutModal({{ $activeOrder->id }})"
                class="bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl hover:bg-emerald-600 transition text-xs sm:text-sm shadow-md shadow-emerald-500/20 cursor-pointer">
                Thanh toán ngay
            </button>
        </div>
    </div>
@endif
