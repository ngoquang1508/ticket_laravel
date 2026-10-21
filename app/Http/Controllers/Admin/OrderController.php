<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng có tìm kiếm, bộ lọc và thống kê tổng quan
     */
    public function index(Request $request, BookingService $bookingService): View
    {
        // Tự động quét và hủy các đơn hàng pending đã quá hạn
        $bookingService->cancelExpiredOrders();

        // Thống kê nhanh cho các thẻ metric
        $metrics = [
            'total' => Order::count(),
            'completed' => Order::where('status', 'completed')->count(),
            'pending' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'cancelled' => Order::whereIn('status', ['cancelled', 'expired'])->count(),
            'revenue' => (float) Order::where('status', 'completed')->sum('total_amount'),
        ];

        // Xây dựng truy vấn danh sách đơn hàng
        $query = Order::with(['event.location', 'user', 'items.ticketType'])
            ->withCount('tickets')
            ->latest('id');

        // Tìm kiếm theo mã đơn hoặc thông tin khách hàng
        if ($request->filled('search')) {
            $keyword = trim($request->string('search'));
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', "%{$keyword}%")
                    ->orWhere('customer_name', 'like', "%{$keyword}%")
                    ->orWhere('customer_email', 'like', "%{$keyword}%")
                    ->orWhere('customer_phone', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo sự kiện
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->integer('event_id'));
        }

        // Lọc theo trạng thái đơn hàng (sử dụng Scope chuẩn hóa từ Model Order)
        if ($request->filled('status')) {
            $query->filterStatus($request->string('status')->toString());
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        // Lọc theo khoảng ngày tạo
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->string('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->string('date_to'));
        }

        $orders = $query->paginate(12)->withQueryString();

        // Danh sách sự kiện phục vụ cho dropdown lọc
        $events = Event::select('id', 'name')->orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'metrics', 'events'));
    }

    /**
     * Xem chi tiết một đơn hàng
     */
    public function show(Order $order): View
    {
        $order->load([
            'event.location',
            'user',
            'items.ticketType',
            'tickets.seat',
            'tickets.orderItem.ticketType',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Admin xác nhận thanh toán thành công cho đơn hàng (pending / processing)
     */
    public function confirm(Request $request, Order $order, BookingService $bookingService): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $bookingService->adminConfirmOrder($order);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Đã xác nhận thanh toán thành công cho đơn hàng #{$order->order_code}!",
                ]);
            }

            return back()->with('success', "Đã xác nhận thanh toán thành công cho đơn hàng #{$order->order_code}!");
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xác nhận đơn: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Lỗi xác nhận đơn: ' . $e->getMessage());
        }
    }

    /**
     * Admin hủy đơn hàng, hoàn trả số lượng vé và giải phóng ghế ngồi
     */
    public function cancel(Request $request, Order $order, BookingService $bookingService): RedirectResponse
    {
        try {
            $reason = $request->filled('cancel_reason')
                ? trim($request->string('cancel_reason'))
                : 'Quản trị viên đã hủy đơn hàng';

            $bookingService->cancelOrder($order, 'cancelled', $reason);

            return back()->with('success', "Đã hủy đơn hàng #{$order->order_code} và hoàn trả vé/ghế thành công!");
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi khi hủy đơn hàng: ' . $e->getMessage());
        }
    }
}
