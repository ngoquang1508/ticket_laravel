<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyTicketController extends Controller
{
    /**
     * Danh sách đơn hàng vé của người dùng có lọc theo trạng thái
     */
    public function index(Request $request, BookingService $bookingService): View
    {
        // Tự động kiểm tra và hủy các đơn hàng pending quá hạn thời gian giữ vé của người dùng
        $bookingService->cancelExpiredOrders(auth()->id());

        $status = $request->query('status');

        $orders = Order::with(['event.ticketTypes', 'items.ticketType', 'tickets.orderItem.ticketType', 'tickets.seat'])
            ->forUser(auth()->id())
            ->filterStatus($status)
            ->latest('id')
            ->paginate(8);

        return view('user.tickets.index', compact('orders', 'status'));
    }

    /**
     * Hiển thị chi tiết vé của chủ sở hữu
     */
    public function show(string $ticket_code): View
    {
        $ticket = $this->findTicketOrFail($ticket_code);

        // Kiểm tra quyền sở hữu vé của người dùng đang đăng nhập
        abort_if($ticket->order->user_id !== auth()->id(), 403, 'Bạn không có quyền truy cập vé này.');

        return view('user.tickets.show', compact('ticket'));
    }

    /**
     * Hiển thị chi tiết vé công khai qua link chia sẻ
     */
    public function publicShow(string $ticket_code): View
    {
        $ticket = $this->findTicketOrFail($ticket_code);

        return view('user.tickets.show', compact('ticket'));
    }

    /**
     * Tìm vé theo mã vé kèm đầy đủ các mối quan hệ (Eager Loading)
     */
    protected function findTicketOrFail(string $ticketCode): Ticket
    {
        return Ticket::with(['order.event.location', 'orderItem.ticketType', 'seat'])
            ->where('ticket_code', $ticketCode)
            ->firstOrFail();
    }
}
