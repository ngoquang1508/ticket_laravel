{{-- Cảnh báo nếu đơn đã bị hủy hoặc hết hạn --}}
@if (in_array($order->status, ['cancelled', 'expired']))
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50/70 p-4 text-sm text-red-800">
        <div class="flex items-start gap-3">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">!</span>
            <div>
                <h4 class="font-bold">Đơn hàng này đã bị {{ $order->status === 'expired' ? 'hết hạn giữ chỗ' : 'hủy bỏ' }}</h4>
                <p class="mt-0.5 text-xs text-red-700">
                    Lý do: <span class="font-medium">{{ $order->cancel_reason ?: 'Không có ghi chú lý do' }}</span>
                </p>
                <p class="mt-1 text-xs text-red-600">Tất cả số lượng vé và vị trí ghế liên quan đã được giải phóng hoàn trả về sự kiện.</p>
            </div>
        </div>
    </div>
@endif
