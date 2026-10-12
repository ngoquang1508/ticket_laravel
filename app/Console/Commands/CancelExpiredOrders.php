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
    public function handle(\App\Services\BookingService $bookingService)
    {
        $count = $bookingService->cancelExpiredOrders();
        $this->info("Successfully expired and cancelled {$count} orders with reason 'Hết hạn thanh toán'.");
    }
}
