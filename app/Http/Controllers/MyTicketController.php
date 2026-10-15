<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ticket;

class MyTicketController extends Controller
{
    public function index(Request $request, \App\Services\BookingService $bookingService)
    {
        // Tự động kiểm tra và hủy các đơn hàng pending quá hạn thời gian giữ vé của người dùng
        $bookingService->cancelExpiredOrders(auth()->id());

        $status = $request->query('status');

        $orders = Order::with(['event.ticketTypes', 'items.ticketType', 'tickets.orderItem.ticketType', 'tickets.seat'])
            ->where('user_id', auth()->id())
            ->when($status, function ($query, $status) {
                if ($status === 'pending') {
                    return $query->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING]);
                }
                if ($status === 'completed') {
                    // Đơn hàng thành công và sự kiện vẫn còn hiệu lực (chưa kết thúc)
                    return $query->where('status', Order::STATUS_COMPLETED)
                        ->whereHas('event', function ($q) {
                            $q->where('ends_at', '>=', now());
                        });
                }
                if ($status === 'expired') {
                    // Đơn hàng hết hạn: bao gồm đơn hết hạn giữ chỗ/thanh toán HOẶC đơn đã hoàn thành nhưng sự kiện đã kết thúc
                    return $query->where(function ($q) {
                        $q->where('status', Order::STATUS_EXPIRED)
                            ->orWhere(function ($sub) {
                                $sub->where('status', Order::STATUS_CANCELLED)
                                    ->where('cancel_reason', 'like', '%Hết hạn%');
                            })
                            ->orWhere(function ($sub) {
                                $sub->where('status', Order::STATUS_COMPLETED)
                                    ->whereHas('event', function ($ev) {
                                        $ev->where('ends_at', '<', now());
                                    });
                            });
                    });
                }
                if ($status === 'cancelled') {
                    // Đơn hàng đã hủy (do người dùng hủy hoặc lý do khác ngoài hết hạn)
                    return $query->where('status', Order::STATUS_CANCELLED)
                        ->where(function ($q) {
                            $q->whereNull('cancel_reason')
                                ->orWhere('cancel_reason', 'not like', '%Hết hạn%');
                        });
                }
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('user.tickets.index', compact('orders', 'status'));
    }

    public function show($ticket_code)
    {
        $ticket = Ticket::with(['order.event.location', 'orderItem.ticketType', 'seat'])
            ->where('ticket_code', $ticket_code)
            ->firstOrFail();

        // Check if user owns the ticket
        if ($ticket->order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.tickets.show', compact('ticket'));
    }

    public function publicShow($ticket_code)
    {
        $ticket = Ticket::with(['order.event.location', 'orderItem.ticketType', 'seat'])
            ->where('ticket_code', $ticket_code)
            ->firstOrFail();

        return view('user.tickets.show', compact('ticket'));
    }
}
