{{-- Thông tin thanh toán --}}
<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs">
    <h3 class="font-bold text-gray-900 border-b pb-3 mb-4">Thông tin thanh toán</h3>
    
    <div class="mb-4 rounded-xl bg-gray-50 p-4 border border-gray-100">
        <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold block">Tổng thanh toán</span>
        <span class="text-2xl font-black text-primary-600 font-mono mt-1 block">
            {{ number_format($order->total_amount, 0, ',', '.') }}đ
        </span>
    </div>

    <dl class="space-y-3 text-xs">
        <div class="flex justify-between">
            <dt class="text-gray-500">Phương thức:</dt>
            <dd class="font-semibold text-gray-900">
                @if ($order->payment_method === 'vnpay')
                    VNPay QR / Thẻ nội địa
                @elseif ($order->payment_method === 'direct')
                    Trực tiếp / Chuyển khoản
                @elseif ($order->payment_method === 'free')
                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700 font-semibold border border-emerald-200">
                        Miễn phí (0đ)
                    </span>
                @else
                    <span class="text-gray-400 font-normal">Chưa chọn (Đang giữ chỗ)</span>
                @endif
            </dd>
        </div>

        <div class="flex justify-between">
            <dt class="text-gray-500">Trạng thái thanh toán:</dt>
            <dd class="font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-700' }}">
                @if ($order->payment_method === 'free')
                    Miễn phí (Không cần thanh toán)
                @else
                    {{ $order->payment_status === 'paid' ? 'Đã thanh toán đủ' : ($order->payment_status === 'failed' ? 'Thất bại' : 'Chưa thanh toán') }}
                @endif
            </dd>
        </div>

        @if ($order->vnp_txn_ref)
            <div class="flex justify-between border-t pt-2">
                <dt class="text-gray-500">Mã GD VNPay:</dt>
                <dd class="font-mono text-gray-700">{{ $order->vnp_txn_ref }}</dd>
            </div>
        @endif

        @if ($order->expires_at && in_array($order->status, ['pending', 'processing']))
            <div class="flex justify-between border-t pt-2">
                <dt class="text-gray-500">
                    {{ $order->status === 'pending' ? 'Hạn giữ chỗ (10p):' : 'Hạn hoàn tất chuyển khoản:' }}
                </dt>
                <dd class="font-medium {{ $order->expires_at > now() ? 'text-amber-700' : 'text-red-600' }}">
                    {{ $order->expires_at->format('d/m/Y H:i:s') }}
                    @if ($order->expires_at > now())
                        (còn {{ (int) ceil(now()->diffInSeconds($order->expires_at) / 60) }}p)
                    @else
                        (Đã hết hạn)
                    @endif
                </dd>
            </div>
        @endif
    </dl>
</div>
