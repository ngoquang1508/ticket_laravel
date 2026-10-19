<?php

namespace App\Services\Booking;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Notifications\AdminOrderNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * OrderPaymentService
 *
 * Chịu trách nhiệm xác nhận thanh toán (VNPay / Trực tiếp), chuyển trạng thái đơn hàng,
 * cập nhật số lượng vé đã bán (sold_quantity), đổi trạng thái ghế sang 'sold',
 * và gửi thông báo/email cho khách hàng và Admin.
 */
class OrderPaymentService
{
    /**
     * Xác nhận đơn sau khi thanh toán thành công qua cổng thanh toán (VNPay hoặc tiền mặt).
     *
     * @throws \Exception
     */
    public function confirmOrder(Order $order, string $paymentRef = '', string $paymentMethod = 'direct'): void
    {
        DB::transaction(function () use ($order, $paymentRef, $paymentMethod) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($locked->status !== 'pending') {
                throw new \Exception('Đơn hàng không ở trạng thái chờ thanh toán. Trạng thái hiện tại: ' . $locked->status);
            }

            if (now() > $locked->expires_at) {
                throw new \Exception('Đơn hàng đã hết hạn thanh toán.');
            }

            $isAssignedSeat = $locked->event->sale_mode === 'assigned_seat';

            $locked->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'vnp_txn_ref' => $paymentRef ?: null,
            ]);

            $locked->items()->update(['status' => 'completed']);

            foreach ($locked->items as $item) {
                TicketType::where('id', $item->ticket_type_id)
                    ->increment('sold_quantity', $item->quantity);

                if ($isAssignedSeat) {
                    $ticketIds = Ticket::where('order_item_id', $item->id)
                        ->whereNotNull('seat_id')
                        ->pluck('seat_id');

                    Seat::whereIn('id', $ticketIds)->update(['status' => 'sold']);
                }
            }
        });

        // Gửi email xác nhận
        try {
            Mail::to($order->user->email)->send(new OrderConfirmed($order));
        } catch (\Throwable $e) {
            Log::warning('Lỗi gửi email xác nhận đơn hàng: ' . $e->getMessage());
        }

        // Bắn thông báo cho toàn bộ Admin
        $this->notifyAdmins(AdminOrderNotification::paid($order));
    }

    /**
     * Xác nhận phương thức thanh toán trực tiếp (chờ admin kiểm tra và duyệt sau).
     * Chuyển trạng thái đơn sang 'processing'.
     *
     * @throws \Exception
     */
    public function markAsProcessing(Order $order, ?\Carbon\Carbon $expireAt = null): void
    {
        DB::transaction(function () use ($order, $expireAt) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!in_array($locked->status, [Order::STATUS_PENDING, Order::STATUS_PROCESSING])) {
                throw new \Exception('Đơn hàng không ở trạng thái chờ thanh toán.');
            }

            if (now() > $locked->expires_at) {
                throw new \Exception('Đơn hàng đã hết hạn thanh toán.');
            }

            $updateData = [
                'status' => Order::STATUS_PROCESSING,
                'payment_method' => Order::PAYMENT_METHOD_DIRECT,
            ];

            if ($expireAt) {
                $updateData['expires_at'] = $expireAt;
            }

            $locked->update($updateData);
        });

        // Bắn thông báo đơn chờ duyệt cho Admin
        $this->notifyAdmins(AdminOrderNotification::awaitingConfirmation($order));
    }

    /**
     * Admin duyệt đơn hàng thanh toán trực tiếp (chuyển sang 'completed').
     *
     * @throws \Exception
     */
    public function adminConfirmOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($locked->status !== Order::STATUS_PROCESSING) {
                throw new \Exception('Chỉ có thể xác nhận thanh toán khi đơn hàng ở trạng thái Chờ duyệt (processing). Đơn hàng đang trong thời gian giữ vé chưa thể xác nhận.');
            }

            $isAssignedSeat = $locked->event->sale_mode === 'assigned_seat';

            $locked->update([
                'status' => 'completed',
                'payment_status' => 'paid',
            ]);

            $locked->items()->update(['status' => 'completed']);

            foreach ($locked->items as $item) {
                TicketType::where('id', $item->ticket_type_id)
                    ->increment('sold_quantity', $item->quantity);

                if ($isAssignedSeat) {
                    $ticketIds = Ticket::where('order_item_id', $item->id)
                        ->whereNotNull('seat_id')
                        ->pluck('seat_id');

                    Seat::whereIn('id', $ticketIds)->update(['status' => 'sold']);
                }
            }
        });

        try {
            $recipientEmail = $order->user?->email ?: $order->customer_email;
            if ($recipientEmail) {
                Mail::to($recipientEmail)->send(new OrderConfirmed($order));
            }
        } catch (\Throwable $e) {
            Log::warning('Không thể gửi email xác nhận đơn hàng: ' . $e->getMessage());
        }

        $this->notifyAdmins(AdminOrderNotification::paid($order));
    }

    /**
     * Gửi thông báo đến toàn bộ tài khoản Admin trong hệ thống.
     */
    public function notifyAdmins($notification): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, $notification);
            }
        } catch (\Throwable $e) {
            Log::warning('Lỗi gửi thông báo cho Admin: ' . $e->getMessage());
        }
    }
}
