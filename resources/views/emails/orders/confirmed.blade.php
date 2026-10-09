<x-mail::message>
# Xác nhận thanh toán thành công

Chào {{ $order->user->name }},

Cảm ơn bạn đã tin tưởng và đặt vé. Đơn hàng **{{ $order->order_code }}** của bạn đã được xác nhận thanh toán thành công.

<x-mail::panel>
**Sự kiện:** {{ $order->event->name }}<br>
**Số lượng vé:** {{ $order->items->count() }}<br>
**Tổng thanh toán:** {{ number_format($order->total_amount, 0, ',', '.') }} đ
</x-mail::panel>

<x-mail::button :url="route('orders.success', $order->order_code)">
Xem vé của bạn
</x-mail::button>

Cảm ơn bạn,<br>
{{ config('app.name') }}
</x-mail::message>
