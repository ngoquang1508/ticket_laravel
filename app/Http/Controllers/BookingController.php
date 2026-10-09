<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function show(string $slug)
    {
        $event = Event::with(['ticketTypes', 'seatMap'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $soldSeats = \App\Models\Seat::where('event_id', $event->id)->where('status', 'sold')->pluck('seat_number')->toArray();
        $heldSeats = \App\Models\Seat::where('event_id', $event->id)->where('status', 'held')->pluck('seat_number')->toArray();

        return view('booking.show', compact('event', 'soldSeats', 'heldSeats'));
    }

    public function store(Request $request, string $slug, \App\Services\BookingService $bookingService)
    {
        $event = Event::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        try {
            // Cart data is sent as JSON in 'cart_data' hidden input
            \Illuminate\Support\Facades\Log::info('Received cart_data:', ['data' => $request->input('cart_data')]);
            $cartData = json_decode($request->input('cart_data'), true) ?? [];
            
            $input = $event->sale_mode === 'assigned_seat' 
                ? ['seats' => implode(',', array_keys($cartData))]
                : ['tickets' => $cartData];

            $order = $bookingService->createOrder($event, $input);
            
            // Redirect to checkout
            return redirect()->route('orders.checkout', $order->order_code)->with('success', 'Giữ vé thành công! Vui lòng hoàn tất trong 10 phút.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkout(\App\Models\Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending' || now() > $order->expires_at) {
            return redirect()->route('home')->with('error', 'Đơn hàng đã hết hạn hoặc bị hủy.');
        }

        return view('booking.checkout', compact('order'));
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
                // For direct payment, extend expiry to 24 hours
                $order->update([
                    'expires_at' => now()->addHours(24),
                    'payment_method' => 'direct'
                ]);
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
            return redirect()->route('orders.checkout', $order->order_code)->with('error', 'Thanh toán thất bại: ' . $result['message']);
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
        if ($order->user_id !== auth()->id() || $order->status !== 'pending' || $order->payment_method !== 'direct') {
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
            $bookingService->cancelOrder($order, 'cancelled');
            if (request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('home')->with('success', 'Đã hủy đơn hàng và giải phóng vé.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
