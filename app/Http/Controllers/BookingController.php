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

        // Tương lai sẽ có query vào bảng OrderItems để lấy ghế đã bán
        $soldSeats = [];
        $heldSeats = [];

        return view('booking.show', compact('event', 'soldSeats', 'heldSeats'));
    }
}
