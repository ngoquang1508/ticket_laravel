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
            return redirect()->route('orders.checkout', $order->id)->with('success', 'Giữ vé thành công! Vui lòng hoàn tất trong 10 phút.');
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

    public function confirm(\App\Models\Order $order, \App\Services\BookingService $bookingService)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $bookingService->confirmOrder($order, 'MOCK_REF_' . time(), 'direct');
            return redirect()->route('home')->with('success', 'Đặt vé thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(\App\Models\Order $order, \App\Services\BookingService $bookingService)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $bookingService->cancelOrder($order, 'cancelled');
            return redirect()->route('home')->with('success', 'Đã hủy đơn hàng và giải phóng vé.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
