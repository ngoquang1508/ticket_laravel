<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = \App\Models\Order::with(['event', 'items', 'tickets'])
            ->where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                $isAssignedSeat = $order->event->sale_mode === 'assigned_seat';

                // Update order to cancelled
                $order->update(['status' => 'cancelled']);

                // Update items to cancelled
                foreach ($order->items as $item) {
                    $item->update(['status' => 'cancelled']);
                }

                // Release seats
                foreach ($order->tickets as $ticket) {
                    if ($ticket->seat_id) {
                        \App\Models\Seat::where('id', $ticket->seat_id)->update(['status' => 'available']);
                    }
                }

                // Return quantity ONLY if not assigned_seat
                if (!$isAssignedSeat) {
                    foreach ($order->items as $item) {
                        \App\Models\TicketType::where('id', $item->ticket_type_id)->increment('quantity', $item->quantity);
                    }
                }

                // Do not delete order or items. Keep them for history.
            });
            $count++;
            $this->info("Expired order: {$order->order_code}");
        }

        $this->info("Successfully expired {$count} orders.");
    }
}
