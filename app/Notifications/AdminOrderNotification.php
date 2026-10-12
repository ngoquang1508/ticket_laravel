<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminOrderNotification extends Notification
{
    use Queueable;

    /**
     * Dữ liệu khởi tạo thông báo
     */
    public function __construct(
        public Order $order,
        public string $type = 'order_paid', // order_paid, order_processing, order_cancelled
        public ?string $customTitle = null,
        public ?string $customMessage = null
    ) {}

    /**
     * Kênh gửi thông báo (Lưu vào cơ sở dữ liệu)
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Tạo thông báo đơn mới thanh toán thành công
     */
    public static function paid(Order $order): self
    {
        $amountFormatted = number_format($order->total_amount, 0, ',', '.') . 'đ';
        $eventName = $order->event?->name ?? 'Sự kiện';
        $customer = $order->customer_name ?: ($order->user?->name ?? 'Khách hàng');

        return new self(
            order: $order,
            type: 'order_paid',
            customTitle: "Đơn hàng mới thanh toán #{$order->order_code}",
            customMessage: "{$customer} vừa thanh toán {$amountFormatted} cho {$eventName}."
        );
    }

    /**
     * Tạo thông báo đơn hàng chờ duyệt thanh toán (chuyển khoản/tiền mặt)
     */
    public static function awaitingConfirmation(Order $order): self
    {
        $amountFormatted = number_format($order->total_amount, 0, ',', '.') . 'đ';
        $customer = $order->customer_name ?: ($order->user?->name ?? 'Khách hàng');

        return new self(
            order: $order,
            type: 'order_processing',
            customTitle: "Đơn hàng chờ xác nhận #{$order->order_code}",
            customMessage: "{$customer} đã đặt vé ({$amountFormatted}), vui lòng kiểm tra thanh toán."
        );
    }

    /**
     * Dữ liệu mảng lưu vào cột `data` của bảng notifications
     */
    public function toArray(object $notifiable): array
    {
        $amount = (float) $this->order->total_amount;
        $customer = $this->order->customer_name ?: ($this->order->user?->name ?? 'Khách hàng');
        $eventName = $this->order->event?->name ?? 'Sự kiện';

        $title = $this->customTitle ?? "Cập nhật đơn hàng #{$this->order->order_code}";
        $message = $this->customMessage ?? "Đơn hàng #{$this->order->order_code} có cập nhật mới.";

        $iconType = match ($this->type) {
            'order_paid' => 'success',
            'order_processing' => 'warning',
            'order_cancelled' => 'danger',
            default => 'info',
        };

        return [
            'type' => $this->type,
            'title' => $title,
            'message' => $message,
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'total_amount' => $amount,
            'customer_name' => $customer,
            'event_name' => $eventName,
            'url' => route('admin.orders.show', $this->order->order_code),
            'icon_type' => $iconType,
        ];
    }
}
