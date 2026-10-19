<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\ProcessPaymentRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateTicketsRequest;
use App\Models\Event;
use App\Models\Order;
use App\Models\Seat;
use App\Services\BookingService;
use App\Services\VNPayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Hiển thị trang chọn vé sự kiện (bản đồ ghế hoặc danh sách vé tự do).
     */
    public function show(string $slug): View|\Illuminate\Http\RedirectResponse
    {
        $event = Event::with(['ticketTypes', 'seatMap'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Chặn truy cập nếu sự kiện chưa đến giờ mở bán vé
        if (!$event->isSaleStarted()) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'Sự kiện chưa đến thời gian mở bán vé (Dự kiến mở bán lúc: ' . $event->ticket_sale_starts_at?->format('H:i, d/m/Y') . ').');
        }

        $userId = auth()->id();
        $activeOrder = null;

        if ($userId) {
            $this->bookingService->cancelExpiredOrders($userId);
            $activeOrder = $this->bookingService->getActivePendingOrder($event, $userId);
        }

        $soldSeats = Seat::where('event_id', $event->id)->where('status', 'sold')->pluck('seat_number')->toArray();
        $heldSeats = Seat::where('event_id', $event->id)->where('status', 'held')->pluck('seat_number')->toArray();

        return view('booking.show', compact('event', 'soldSeats', 'heldSeats', 'activeOrder'));
    }

    /**
     * Tạo đơn hàng giữ vé khi người dùng bấm Tiếp tục thanh toán.
     */
    public function store(StoreBookingRequest $request, string $slug): RedirectResponse
    {
        $event = Event::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $userId = auth()->id();

        // 1. Quét dọn các đơn hết hạn của người dùng
        $this->bookingService->cancelExpiredOrders($userId);

        // 2. Chặn nếu người dùng đang có đơn giữ vé còn thời hạn
        $existingOrder = $this->bookingService->getActivePendingOrder($event, $userId);
        if ($existingOrder) {
            return redirect()->route('events.book', $event->slug)
                ->with('toast_title', 'Đang có đơn giữ vé')
                ->with('error', 'Bạn đang có đơn hàng giữ vé #' . $existingOrder->order_code . ' cho sự kiện này. Vui lòng thanh toán hoặc hủy đơn trước khi đặt mới.');
        }

        try {
            $cartData = $request->getCartData();
            $input = $event->sale_mode === 'assigned_seat'
                ? ['seats' => implode(',', array_keys($cartData))]
                : ['tickets' => $cartData];

            $order = $this->bookingService->createOrder($event, $input);

            return redirect()->route('events.book', $event->slug)
                ->with('active_checkout_order', $order->order_code)
                ->with('success', 'Giữ vé thành công! Vui lòng hoàn tất thanh toán trong 10 phút.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Xử lý điều hướng thanh toán (VNPay hoặc Trực tiếp).
     */
    public function processPayment(ProcessPaymentRequest $request, Order $order, VNPayService $vnpayService): RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending' || now() > $order->expires_at) {
            return redirect()->route('home')->with('error', 'Đơn hàng đã hết hạn hoặc bị hủy.');
        }

        $method = $request->input('payment_method');

        // Cập nhật thông tin người nhận vé vào đơn hàng
        $order->update([
            'customer_name'  => $request->input('customer_name', $order->customer_name),
            'customer_email' => $request->input('customer_email', $order->customer_email),
            'customer_phone' => $request->input('customer_phone', $order->customer_phone),
        ]);

        // Tự động lưu số điện thoại vào thông tin tài khoản nếu người dùng chưa có
        $currentUser = auth()->user();
        if ($currentUser && empty($currentUser->phone) && !empty($request->customer_phone)) {
            $currentUser->update(['phone' => $request->customer_phone]);
        }

        try {
            if ($method === 'vnpay') {
                $paymentUrl = $vnpayService->createPaymentUrl($order);
                return redirect($paymentUrl);
            }

            if ($method === 'direct') {
                if (!$order->event->canPayDirect()) {
                    return back()->with('error', 'Hình thức thanh toán trực tiếp đã ngừng nhận cho sự kiện này (chỉ áp dụng trước 48 giờ khi sự kiện diễn ra). Vui lòng chọn thanh toán qua VNPay.');
                }

                $expireAt = now()->addHours(24);
                if ($order->event->starts_at && $expireAt->greaterThan($order->event->starts_at)) {
                    $expireAt = $order->event->starts_at;
                }

                $this->bookingService->markAsProcessing($order, $expireAt);

                return redirect()->route('orders.instruction', $order->order_code);
            }

            return back()->with('error', 'Phương thức thanh toán không hợp lệ.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Nhận kết quả phản hồi từ cổng thanh toán VNPay.
     */
    public function vnpayReturn(Request $request, VNPayService $vnpayService): RedirectResponse
    {
        $result = $vnpayService->verifyPayment($request->all());

        if (!$result['order_code']) {
            return redirect()->route('home')->with('error', 'Giao dịch không hợp lệ.');
        }

        $order = Order::where('order_code', $result['order_code'])->first();
        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($result['success']) {
            try {
                if ($order->status === 'pending') {
                    $this->bookingService->confirmOrder($order, $result['transaction_no'], 'vnpay');
                }
                return redirect()->route('orders.success', $order->order_code);
            } catch (\Exception $e) {
                return redirect()->route('home')->with('error', 'Lỗi xử lý đơn hàng: ' . $e->getMessage());
            }
        }

        return redirect()->route('user.tickets')
            ->with('active_checkout_order', $order->order_code)
            ->with('error', 'Thanh toán thất bại: ' . $result['message']);
    }

    /**
     * Trang hoàn tất thanh toán thành công.
     */
    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'completed') {
            return redirect()->route('home');
        }
        return view('booking.success', compact('order'));
    }

    /**
     * Trang hướng dẫn chuyển khoản trực tiếp.
     */
    public function instruction(Order $order)
    {
        if ($order->user_id !== auth()->id() || !in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PROCESSING]) || $order->payment_method !== Order::PAYMENT_METHOD_DIRECT) {
            return redirect()->route('home');
        }
        return view('booking.instruction', compact('order'));
    }

    /**
     * Hủy đơn hàng và giải phóng vé/ghế.
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        try {
            $this->bookingService->cancelOrder($order, 'cancelled', 'Người dùng hủy đơn');
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

    /**
     * Cập nhật số lượng vé cho đơn hàng tự do.
     */
    public function updateTickets(UpdateTicketsRequest $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        try {
            $this->bookingService->updateOrderTickets($order, $request->input('tickets', []));

            $status = $request->input('status', $order->status);
            $page = $request->input('page');
            $params = array_filter(['status' => $status, 'page' => $page]);

            $targetUrl = route('user.tickets', $params) . '#order-' . $order->id;

            return redirect()->to($targetUrl)->with('success', 'Đã cập nhật số lượng vé thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Giao diện chỉnh sửa ghế cho đơn hàng sơ đồ ghế.
     */
    public function editTickets(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending' || now() > $order->expires_at) {
            if ($order->status === 'pending' && now() > $order->expires_at) {
                $this->bookingService->cancelOrder($order, 'cancelled', 'Hết hạn thanh toán');
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

        $soldSeats = Seat::where('event_id', $event->id)->where('status', 'sold')->pluck('seat_number')->toArray();
        $currentSeatNumbers = $order->tickets->pluck('seat.seat_number')->filter()->values()->toArray();
        $heldSeats = Seat::where('event_id', $event->id)
            ->where('status', 'held')
            ->whereNotIn('seat_number', $currentSeatNumbers)
            ->pluck('seat_number')
            ->toArray();

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

    /**
     * Cập nhật danh sách ghế đã chọn cho đơn hàng.
     */
    public function updateAssignedSeats(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        try {
            $cartData = json_decode($request->input('cart_data'), true) ?? [];
            if (empty($cartData)) {
                return back()->with('error', 'Vui lòng chọn ít nhất 1 ghế. Nếu không muốn đặt vé nữa, bạn có thể hủy đơn hàng.');
            }

            $seatNumbers = array_keys($cartData);
            $this->bookingService->updateAssignedSeatOrder($order, $seatNumbers);

            $status = $request->input('status', $order->status);
            $page = $request->input('page');
            $params = array_filter(['status' => $status, 'page' => $page]);

            $targetUrl = route('user.tickets', $params) . '#order-' . $order->id;

            return redirect()->to($targetUrl)->with('success', 'Đã cập nhật vé thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Kiểm tra quyền sở hữu đơn hàng của người dùng đang đăng nhập.
     */
    protected function authorizeOrder(Order $order): void
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Bạn không có quyền thao tác trên đơn hàng này.');
    }
}
