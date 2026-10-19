<?php

namespace App\Services\Booking;

use App\Models\Order;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;

/**
 * OrderCancellationService
 *
 * Chịu trách nhiệm hủy đơn hàng, giải phóng ghế đang giữ (held -> available),
 * hoàn trả số lượng vé vào kho tồn của TicketType, và quét tự động các đơn hết hạn giữ vé.
 */
class OrderCancellationService
{
    /**
     * Hủy đơn hàng và giải phóng ghế / hoàn trả số lượng vé.
     */
    public function cancelOrder(Order $order, string $newStatus = Order::STATUS_CANCELLED, ?string $cancelReason = null): void
    {
        DB::transaction(function () use ($order, $newStatus, $cancelReason) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$locked) {
                return;
            }

            // Sử dụng switch-case để kiểm tra trạng thái đơn hàng có được phép hủy hay không
            switch ($locked->status) {
                case Order::STATUS_PENDING:
                case Order::STATUS_PROCESSING:
                    // Trạng thái hợp lệ, tiếp tục thực hiện quy trình hủy đơn và hoàn trả vé
                    break;

                case Order::STATUS_COMPLETED:
                case Order::STATUS_CANCELLED:
                case Order::STATUS_EXPIRED:
                default:
                    // Đơn hàng đã hoàn tất hoặc đã bị hủy trước đó -> không xử lý lại
                    return;
            }

            // Chuẩn hóa trạng thái mới của đơn hàng
            switch ($newStatus) {
                case Order::STATUS_EXPIRED:
                    $targetStatus = Order::STATUS_EXPIRED;
                    break;

                case Order::STATUS_CANCELLED:
                default:
                    $targetStatus = Order::STATUS_CANCELLED;
                    break;
            }

            $updateData = ['status' => $targetStatus];
            if ($cancelReason) {
                $updateData['cancel_reason'] = $cancelReason;
            }
            $locked->update($updateData);

            // Cập nhật trạng thái các item sang cancelled để lưu lịch sử
            $locked->items()->update(['status' => Order::STATUS_CANCELLED]);

            // Xử lý giải phóng tài nguyên ghế dựa theo chế độ bán vé của sự kiện
            switch ($locked->event?->sale_mode) {
                case 'assigned_seat':
                    // Giải phóng các ghế đang giữ chuyển trạng thái về available
                    $tickets = Ticket::where('order_id', $locked->id)->get();
                    foreach ($tickets as $ticket) {
                        if ($ticket->seat_id) {
                            Seat::where('id', $ticket->seat_id)->update(['status' => 'available']);
                        }
                    }
                    break;

                default:
                    // Với sự kiện vé tự do, không có ghế cụ thể để giải phóng
                    break;
            }

            // Hoàn lại số lượng vé vào kho tồn
            foreach ($locked->items as $item) {
                TicketType::where('id', $item->ticket_type_id)
                    ->increment('quantity', $item->quantity);
            }
        });
    }

    /**
     * Tự động quét và hủy các đơn hàng đã quá hạn giữ vé.
     *
     * @param int|null $userId Nếu truyền thì chỉ quét cho user đó, nếu null quét toàn hệ thống.
     * @return int Số lượng đơn hàng đã hủy.
     */
    public function cancelExpiredOrders(?int $userId = null): int
    {
        $query = Order::whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
            ->where('expires_at', '<=', now());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $expiredOrders = $query->get();
        $count = 0;

        foreach ($expiredOrders as $order) {
            $this->cancelOrder($order, Order::STATUS_CANCELLED, 'Hết hạn thanh toán');
            $count++;
        }

        return $count;
    }
}
