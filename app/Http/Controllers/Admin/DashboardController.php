<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use App\Models\Order;
use App\Models\TicketType;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'eventCount' => Event::count(),
            'publishedEventCount' => Event::where('is_published', true)->count(),
            'orderCount' => Order::count(),
            'locationCount' => Location::count(),
            'categoryCount' => Category::count(),
            'ticketTypeCount' => TicketType::count(),
            'recentEvents' => Event::with('location')->latest()->limit(5)->get(),
        ]);
    }
}
