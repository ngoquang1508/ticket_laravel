<x-mail::message>
# Xác nhận thanh toán thành công

Chào **{{ $order->customer_name ?: $order->user->name }}**,

Cảm ơn bạn đã tin tưởng và đặt vé tại **{{ config('app.name') }}**. Đơn hàng **#{{ $order->order_code }}** của bạn đã được xác nhận thanh toán thành công.

<x-mail::panel>
### Thông tin đơn hàng
- **Mã đơn hàng:** `{{ $order->order_code }}`
- **Sự kiện:** {{ $order->event->name }}
- **Thời gian:** {{ $order->event->starts_at ? $order->event->starts_at->format('H:i, d/m/Y') : 'Đang cập nhật' }}{{ $order->event->ends_at ? ' - ' . $order->event->ends_at->format('H:i, d/m/Y') : '' }}
- **Địa điểm:** {{ $order->event->location ? ($order->event->location->name . ($order->event->location->address ? ' (' . $order->event->location->address . ')' : '')) : 'Đang cập nhật' }}
- **Phương thức thanh toán:** {{ $order->payment_method === 'vnpay' ? 'VNPay (Quét QR / Thẻ ATM / Visa)' : ($order->payment_method === 'direct' ? 'Thanh toán trực tiếp / Chuyển khoản' : ($order->payment_method ?? 'Trực tuyến')) }}
- **Thời gian thanh toán:** {{ $order->updated_at ? $order->updated_at->format('H:i, d/m/Y') : now()->format('H:i, d/m/Y') }}
- **Tổng thanh toán:** **{{ number_format($order->total_amount, 0, ',', '.') }} đ**
</x-mail::panel>

### Chi tiết vé đã đặt:
<x-mail::table>
| Loại vé | Số lượng | Đơn giá | Thành tiền |
| :--- | :---: | :---: | ---: |
@foreach ($order->items as $item)
| {{ $item->ticketType->name }} | {{ $item->quantity }} | {{ number_format($item->price, 0, ',', '.') }} đ | {{ number_format($item->total, 0, ',', '.') }} đ |
@endforeach
</x-mail::table>

Bạn có thể bấm vào nút bên dưới để xem vé điện tử và mã QR check-in sự kiện:

<x-mail::button :url="route('orders.success', $order->order_code)">
Xem vé của bạn
</x-mail::button>

*Lưu ý: Vui lòng mang theo vé điện tử (hoặc lưu ảnh mã QR) đến quầy check-in trước giờ sự kiện bắt đầu ít nhất 15-30 phút.*

Cảm ơn bạn,<br>
{{ config('app.name') }}
</x-mail::message>
