<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển quản trị viên với các chỉ số thống kê và biểu đồ
     */
    public function __invoke(Request $request): View
    {
        // 1. Nhận các tham số bộ lọc
        $period = $request->string('period')->toString() ?: '30days';
        $eventId = $request->filled('event_id') ? $request->integer('event_id') : null;
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // 2. Xác định khoảng thời gian lọc dữ liệu
        $startDate = null;
        $endDate = Carbon::now()->endOfDay();
        $chartStartDate = null;
        $chartEndDate = $endDate->copy();
        $chartGroupBy = 'day';

        if ($period === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $chartStartDate = $startDate->copy();
            $chartGroupBy = 'hour';
        } elseif ($period === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $chartStartDate = $startDate->copy();
        } elseif ($period === '30days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $chartStartDate = $startDate->copy();
        } elseif ($period === 'this_month') {
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $chartStartDate = $startDate->copy();
        } elseif ($period === 'custom') {
            if ($dateFrom) {
                $startDate = Carbon::parse($dateFrom)->startOfDay();
            }
            if ($dateTo) {
                $endDate = Carbon::parse($dateTo)->endOfDay();
            }
            $chartStartDate = $startDate ? $startDate->copy() : Carbon::now()->subDays(29)->startOfDay();
            $chartEndDate = $endDate ? $endDate->copy() : Carbon::now()->endOfDay();
        } elseif ($period === 'all') {
            $firstOrder = Order::orderBy('created_at', 'asc')->first();
            $chartStartDate = ($firstOrder && $firstOrder->created_at)
                ? $firstOrder->created_at->copy()->startOfDay()
                : Carbon::now()->subDays(6)->startOfDay();
        }

        // 3. Khởi tạo truy vấn cơ sở cho đơn hàng áp dụng bộ lọc
        $baseOrderQuery = Order::query()
            ->when($eventId, fn($q) => $q->where('event_id', $eventId))
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('created_at', '<=', $endDate));

        // 4. Các chỉ số thống kê chính
        // Doanh thu thực tế: CHỈ TÍNH đơn hàng có payment_status = 'paid'
        $totalRevenue = (float) (clone $baseOrderQuery)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Tổng số đơn hàng trong kỳ lọc
        $totalOrders = (clone $baseOrderQuery)->count();

        // Số vé đã bán thành công (từ các đơn hàng đã thanh toán)
        $totalTicketsSold = (int) OrderItem::whereHas('order', function ($q) use ($startDate, $endDate, $eventId) {
            $q->where('payment_status', 'paid')
                ->when($eventId, fn($sub) => $sub->where('event_id', $eventId))
                ->when($startDate, fn($sub) => $sub->where('created_at', '>=', $startDate))
                ->when($endDate, fn($sub) => $sub->where('created_at', '<=', $endDate));
        })->sum('quantity');

        // 5. Thống kê đơn hàng theo trạng thái
        $paidOrdersCount = (clone $baseOrderQuery)
            ->where('payment_status', 'paid')
            ->count();

        $pendingOrdersCount = (clone $baseOrderQuery)
            ->where('payment_status', '!=', 'paid')
            ->where(function ($q) {
                $q->where('payment_status', 'pending')
                    ->orWhereIn('status', ['pending', 'processing']);
            })
            ->count();

        $cancelledOrdersCount = (clone $baseOrderQuery)
            ->where('payment_status', '!=', 'paid')
            ->where(function ($q) {
                $q->whereIn('status', ['cancelled', 'expired'])
                    ->orWhere('payment_status', 'failed');
            })
            ->count();

        $statusTotal = $paidOrdersCount + $pendingOrdersCount + $cancelledOrdersCount;
        $orderStatusStats = [
            'paid' => [
                'count' => $paidOrdersCount,
                'percentage' => $statusTotal > 0 ? round(($paidOrdersCount / $statusTotal) * 100, 1) : 0,
            ],
            'pending' => [
                'count' => $pendingOrdersCount,
                'percentage' => $statusTotal > 0 ? round(($pendingOrdersCount / $statusTotal) * 100, 1) : 0,
            ],
            'cancelled' => [
                'count' => $cancelledOrdersCount,
                'percentage' => $statusTotal > 0 ? round(($cancelledOrdersCount / $statusTotal) * 100, 1) : 0,
            ],
        ];

        // 6. Dữ liệu biểu đồ doanh thu theo thời gian (Line/Area Chart)
        $revenueChartLabels = [];
        $revenueChartData = [];

        if ($chartGroupBy === 'hour') {
            $revenueByHour = (clone $baseOrderQuery)
                ->where('payment_status', 'paid')
                ->selectRaw('HOUR(created_at) as order_hour, SUM(total_amount) as total')
                ->groupBy('order_hour')
                ->pluck('total', 'order_hour');

            for ($h = 0; $h <= 23; $h += 2) {
                $revenueChartLabels[] = sprintf('%02d:00', $h);
                $val = (float) ($revenueByHour[$h] ?? 0) + (float) ($revenueByHour[$h + 1] ?? 0);
                $revenueChartData[] = $val;
            }
        } else {
            if ($chartStartDate && $chartEndDate && $chartStartDate->diffInDays($chartEndDate) > 60) {
                $chartStartDate = $chartEndDate->copy()->subDays(59)->startOfDay();
            }

            $revenueByDate = (clone $baseOrderQuery)
                ->where('payment_status', 'paid')
                ->selectRaw('DATE(created_at) as order_date, SUM(total_amount) as total')
                ->groupBy('order_date')
                ->pluck('total', 'order_date');

            $cursor = $chartStartDate ? $chartStartDate->copy() : Carbon::now()->subDays(6)->startOfDay();
            while ($cursor <= $chartEndDate) {
                $dateKey = $cursor->format('Y-m-d');
                $revenueChartLabels[] = $cursor->format('d/m');
                $revenueChartData[] = (float) ($revenueByDate[$dateKey] ?? 0);
                $cursor->addDay();
            }
        }

        // 7. Top tối đa 5 sự kiện bán chạy nhất
        // Tối ưu: Tính toán trực tiếp bằng SQL gom nhóm (GROUP BY) thay vì kéo toàn bộ model vào RAM
        $topEvents = DB::table('events')
            ->join('orders', 'events.id', '=', 'orders.event_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->when($eventId, fn($q) => $q->where('events.id', $eventId))
            ->when($startDate, fn($q) => $q->where('orders.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('orders.created_at', '<=', $endDate))
            ->selectRaw('events.id, events.name, SUM(order_items.quantity) as tickets, SUM(order_items.total) as revenue')
            ->groupBy('events.id', 'events.name')
            ->orderByDesc('tickets')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'tickets' => (int) $event->tickets,
                    'revenue' => (float) $event->revenue,
                ];
            });

        // Tính tỷ lệ % so với sự kiện bán chạy nhất để hiển thị thanh độ dài chuẩn
        $maxTickets = $topEvents->max('tickets') ?: 1;
        $topEvents = $topEvents->map(function ($ev) use ($maxTickets) {
            $ev['percentage'] = round(($ev['tickets'] / $maxTickets) * 100);
            return $ev;
        });

        // 8. Danh sách sự kiện cho dropdown bộ lọc
        $filterEvents = Event::select('id', 'name')->orderBy('name')->get();

        // 9. Sự kiện sắp diễn ra / gần đây
        $upcomingEvents = Event::with('location')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc')
            ->limit(5)
            ->get();

        if ($upcomingEvents->isEmpty()) {
            $upcomingEvents = Event::with('location')->latest()->limit(5)->get();
        }

        // 10. Số liệu tổng quan tài nguyên hệ thống
        $systemMetrics = [
            'events' => Event::count(),
            'publishedEvents' => Event::where('is_published', true)->count(),
            'locations' => Location::count(),
            'categories' => Category::count(),
            'ticketTypes' => TicketType::count(),
            'orders' => Order::count(),
        ];

        return view('admin.dashboard.index', compact(
            'period',
            'eventId',
            'dateFrom',
            'dateTo',
            'totalRevenue',
            'totalOrders',
            'totalTicketsSold',
            'orderStatusStats',
            'revenueChartLabels',
            'revenueChartData',
            'topEvents',
            'filterEvents',
            'upcomingEvents',
            'systemMetrics'
        ));
    }
}
