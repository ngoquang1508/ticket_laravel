<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ticket;

class MyTicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['event', 'items.ticketType', 'tickets.orderItem.ticketType', 'tickets.seat'])
            ->where('user_id', auth()->id())
            ->when($status, function ($query, $status) {
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
