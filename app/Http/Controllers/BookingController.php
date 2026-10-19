<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function show(string $slug, \App\Services\BookingService $bookingService)
    {
        $event = Event::with(['ticketTypes', 'seatMap'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $userId = auth()->id();
        $activeOrder = null;

        if ($userId) {
            $bookingService->cancelExpiredOrders($userId);
            $activeOrder = $bookingService->getActivePendingOrder($event, $userId);
        }

        $soldSeats = \App\Models\Seat::where('event_id', $event->id)->where('status', 'sold')->pluck('seat_number')->toArray();
        $heldSeats = \App\Models\Seat::where('event_id', $event->id)->where('status', 'held')->pluck('seat_number')->toArray();

        return view('booking.show', compact('event', 'soldSeats', 'heldSeats', 'activeOrder'));
    }

    public function store(Request $request, string $slug, \App\Services\BookingService $bookingService)
    {
        $event = Event::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $userId = auth()->id();

        // 1. Quét hủy các đơn đã hết hạn giữ vé của user
        $bookingService->cancelExpiredOrders($userId);

        // 2. Kiểm tra xem user đã có order pending của cùng event còn trong thời gian giữ vé hay chưa
        $existingOrder = $bookingService->getActivePendingOrder($event, $userId);

        if ($existingOrder) {
            // Thông báo người dùng đang có đơn giữ vé chưa thanh toán, không tự động mở modal đơn cũ
            return redirect()->route('events.book', $event->slug)
                ->with('toast_title', 'Đang có đơn giữ vé')
                ->with('error', 'Bạn đang có đơn hàng giữ vé #' . $existingOrder->order_code . ' cho sự kiện này. Vui lòng thanh toán hoặc hủy đơn trước khi đặt mới.');
        }

        try {
            // Cart data is sent as JSON in 'cart_data' hidden input
            \Illuminate\Support\Facades\Log::info('Received cart_data:', ['data' => $request->input('cart_data')]);
            $cartData = json_decode($request->input('cart_data'), true) ?? [];

            $input = $event->sale_mode === 'assigned_seat'
                ? ['seats' => implode(',', array_keys($cartData))]
                : ['tickets' => $cartData];

            $order = $bookingService->createOrder($event, $input);

            // Giữ vé thành công, redirect về trang chọn vé và tự mở modal checkout
            return redirect()->route('events.book', $event->slug)
                ->with('active_checkout_order', $order->order_code)
                ->with('success', 'Giữ vé thành công! Vui lòng hoàn tất thanh toán trong 10 phút.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function processPayment(\Illuminate\Http\Request $request, \App\Models\Order $order, \App\Services\VNPayService $vnpayService, \App\Services\BookingService $bookingService)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending' || now() > $order->expires_at) {
            return redirect()->route('home')->with('error', 'Đơn hàng đã hết hạn hoặc bị hủy.');
        }

        $method = $request->input('payment_method');

        try {
            if ($method === 'vnpay') {
                $paymentUrl = $vnpayService->createPaymentUrl($order);
                return redirect($paymentUrl);
            } elseif ($method === 'direct') {
                // Kiểm tra sự kiện có còn trong thời hạn cho phép thanh toán trực tiếp không (trước 48h)
                if (!$order->event->canPayDirect()) {
                    return back()->with('error', 'Hình thức thanh toán trực tiếp đã ngừng nhận cho sự kiện này (chỉ áp dụng trước 48 giờ khi sự kiện diễn ra). Vui lòng chọn thanh toán qua VNPay.');
                }

                // Gia hạn tối đa 24 giờ nhưng không vượt quá thời điểm bắt đầu sự kiện
                $expireAt = now()->addHours(24);
                if ($order->event->starts_at && $expireAt->greaterThan($order->event->starts_at)) {
                    $expireAt = $order->event->starts_at;
                }

                // Chuyển sang processing và kích hoạt thông báo cho Admin
                $bookingService->markAsProcessing($order, $expireAt);

                return redirect()->route('orders.instruction', $order->order_code);
            } else {
                return back()->with('error', 'Phương thức thanh toán không hợp lệ.');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function vnpayReturn(\Illuminate\Http\Request $request, \App\Services\VNPayService $vnpayService, \App\Services\BookingService $bookingService)
    {
        $result = $vnpayService->verifyPayment($request->all());

        if (!$result['order_code']) {
            return redirect()->route('home')->with('error', 'Giao dịch không hợp lệ.');
        }

        $order = \App\Models\Order::where('order_code', $result['order_code'])->first();
        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($result['success']) {
            try {
                if ($order->status === 'pending') {
                    $bookingService->confirmOrder($order, $result['transaction_no'], 'vnpay');
                }
                return redirect()->route('orders.success', $order->order_code);
            } catch (\Exception $e) {
                return redirect()->route('home')->with('error', 'Lỗi xử lý đơn hàng: ' . $e->getMessage());
            }
        } else {
            return redirect()->route('user.tickets')
                ->with('active_checkout_order', $order->order_code)
                ->with('error', 'Thanh toán thất bại: ' . $result['message']);
        }
    }

    public function success(\App\Models\Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'completed') {
            return redirect()->route('home');
        }
        return view('booking.success', compact('order'));
    }

    public function instruction(\App\Models\Order $order)
    {
        if ($order->user_id !== auth()->id() || !in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_PROCESSING]) || $order->payment_method !== \App\Models\Order::PAYMENT_METHOD_DIRECT) {
            return redirect()->route('home');
        }
        return view('booking.instruction', compact('order'));
    }

    public function cancel(\App\Models\Order $order, \App\Services\BookingService $bookingService)
    {
        if ($order->user_id !== auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        try {
            $bookingService->cancelOrder($order, 'cancelled', 'Người dùng hủy đơn');
            if (request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('user.tickets')->with('success', 'Đã hủy đơn hàng và giải phóng vé.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateTickets(\Illuminate\Http\Request $request, \App\Models\Order $order, \App\Services\BookingService $bookingService)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $tickets = $request->input('tickets', []);
        if (empty($tickets) || !is_array($tickets)) {
            return back()->with('error', 'Dữ liệu số lượng vé không hợp lệ.');
        }

        try {
            $bookingService->updateOrderTickets($order, $tickets);

            // Chuyển hướng về trang my-tickets với đúng trạng thái và thẻ đơn hàng theo id
            $status = $request->input('status', $order->status);
            $page = $request->input('page');
            $params = array_filter([
                'status' => $status,
                'page' => $page,
            ]);

            $targetUrl = route('user.tickets', $params) . '#order-' . $order->id;

            return redirect()->to($targetUrl)->with('success', 'Đã cập nhật số lượng vé thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editTickets(\App\Models\Order $order, \App\Services\BookingService $bookingService)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending' || now() > $order->expires_at) {
            if ($order->status === 'pending' && now() > $order->expires_at) {
                $bookingService->cancelOrder($order, 'cancelled', 'Hết hạn thanh toán');
            }
            $params = array_filter([
                'status' => request('status', 'cancelled'),
                'page' => request('page'),
            ]);
            $targetUrl = route('user.tickets', $params) . '#order-' . $order->id;
            return redirect()->to($targetUrl)->with('error', 'Đơn hàng đã hết thời gian giữ vé và đã bị hủy tự động.');
        }

        if ($order->event->sale_mode !== 'assigned_seat') {
            return redirect()->route('user.tickets')->with('error', 'Chỉ sự kiện có sơ đồ chọn ghế mới hỗ trợ chỉnh sửa ghế trực tiếp.');
        }

        $order->load(['event.ticketTypes', 'event.seatMap', 'tickets.seat', 'items.ticketType']);
        $event = $order->event;

        $soldSeats = \App\Models\Seat::where('event_id', $event->id)->where('status', 'sold')->pluck('seat_number')->toArray();
        $currentSeatNumbers = $order->tickets->pluck('seat.seat_number')->filter()->values()->toArray();
        // Ghế held bởi các đơn hàng khác (loại trừ ghế đang giữ của chính đơn hàng này)
        $heldSeats = \App\Models\Seat::where('event_id', $event->id)
            ->where('status', 'held')
            ->whereNotIn('seat_number', $currentSeatNumbers)
            ->pluck('seat_number')
            ->toArray();

        // Chuẩn bị giỏ hàng ban đầu
        $initialCart = [];
        foreach ($order->tickets as $ticket) {
            if ($ticket->seat) {
                $initialCart[$ticket->seat->seat_number] = [
                    'id' => $ticket->seat->seat_number,
                    'zone' => $ticket->seat->zone ?? '',
                    'price' => (float) ($ticket->orderItem ? $ticket->orderItem->price : ($ticket->seat->ticketType ? $ticket->seat->ticketType->price : 0)),
                    'ticket_id' => $ticket->seat->ticket_type_id,
                ];
            }
        }

        return view('user.tickets.edit_tickets', compact('order', 'event', 'soldSeats', 'heldSeats', 'currentSeatNumbers', 'initialCart'));
    }

    public function updateAssignedSeats(\Illuminate\Http\Request $request, \App\Models\Order $order, \App\Services\BookingService $bookingService)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $cartData = json_decode($request->input('cart_data'), true) ?? [];
            if (empty($cartData)) {
                return back()->with('error', 'Vui lòng chọn ít nhất 1 ghế. Nếu không muốn đặt vé nữa, bạn có thể hủy đơn hàng.');
            }

            $seatNumbers = array_keys($cartData);
            $bookingService->updateAssignedSeatOrder($order, $seatNumbers);

            // Chuyển hướng về trang my-tickets với đúng trạng thái và thẻ đơn hàng theo id
            $status = $request->input('status', $order->status);
            $page = $request->input('page');
            $params = array_filter([
                'status' => $status,
                'page' => $page,
            ]);

            $targetUrl = route('user.tickets', $params) . '#order-' . $order->id;

            return redirect()->to($targetUrl)->with('success', 'Đã cập nhật vé thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
