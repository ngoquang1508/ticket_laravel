<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với đầy đủ các partials: Hero Slider đôi vô hạn (3 ảnh),
     * Xu hướng (10 sự kiện), Lọc thời gian, Điểm đến, Vé bán lại và Danh mục (>= 4 events).
     */
    public function index(): View
    {
        // 1. Hero Banner Slider: Chỉ lấy các sự kiện nổi bật (is_featured = true), chưa kết thúc
        $heroEvents = Event::activeListing()
            ->where('is_featured', true)
            ->where(function ($query) {
                $query->where(function ($sub) {
                    $sub->whereNotNull('ends_at')
                        ->where('ends_at', '>', now());
                })->orWhere(function ($sub) {
                    $sub->whereNull('ends_at')
                        ->where('starts_at', '>=', now());
                });
            })
            ->orderBy('starts_at', 'asc')
            ->take(5)
            ->get();

        // 2. Sự kiện xu hướng: Chỉ lấy các sự kiện sắp diễn ra và THỰC SỰ có đơn hàng thành công (orders_count > 0)
        $trendingEvents = Event::activeListing()
            ->upcoming()
            ->withCount([
                'orders' => function ($query) {
                    $query->whereIn('status', ['processing', 'completed']);
                }
            ])
            ->having('orders_count', '>', 0)
            ->orderByDesc('orders_count')
            ->take(10)
            ->get();

        // 3. Sự kiện lọc theo thời gian: Cuối tuần này & Tháng này
        $startOfWeekend = Carbon::now()->endOfWeek()->subDays(2)->startOfDay(); // Thứ 6
        $endOfWeekend = Carbon::now()->endOfWeek()->endOfDay(); // Chủ nhật
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth()->endOfDay();

        $weekendEvents = Event::activeListing()
            ->whereBetween('starts_at', [$startOfWeekend, $endOfWeekend])
            ->take(8)
            ->get();

        $monthEvents = Event::activeListing()
            ->whereBetween('starts_at', [$startOfMonth, $endOfMonth])
            ->take(8)
            ->get();

        if ($weekendEvents->isEmpty()) {
            $weekendEvents = Event::activeListing()->upcoming()->take(6)->get();
        }
        if ($monthEvents->isEmpty()) {
            $monthEvents = Event::activeListing()->upcoming()->take(6)->get();
        }

        // 4. Khối Vé bán lại (Resale Tickets)
        $resaleCategory = Category::where('slug', 've-ban-lai')->first();

        $resaleEvents = collect();

        if ($resaleCategory) {
            $resaleEvents = Event::activeListing()
                ->upcoming()
                ->whereHas('categories', function ($q) use ($resaleCategory) {
                    $q->where('categories.id', $resaleCategory->id);
                })
                ->take(8)
                ->get();
        }

        // 5. Điểm đến thú vị (3 thành phố chính: TP.HCM, Hà Nội, Đà Nẵng và Vị trí khác)
        // Tối ưu: Đếm trực tiếp từ Database qua 1 câu truy vấn gom nhóm duy nhất, tránh N+1 queries và lãng phí RAM
        $cityCounts = Event::where('is_published', true)
            ->where('starts_at', '>=', now())
            ->leftJoin('locations', 'events.location_id', '=', 'locations.id')
            ->selectRaw("
                COUNT(CASE WHEN locations.address LIKE '%Hồ Chí Minh%' OR locations.address LIKE '%TP.HCM%' OR locations.address LIKE '%TP. HCM%' OR locations.address LIKE '%Sài Gòn%' THEN 1 END) as count_hcm,
                COUNT(CASE WHEN locations.address LIKE '%Hà Nội%' THEN 1 END) as count_hn,
                COUNT(CASE WHEN locations.address LIKE '%Đà Nẵng%' THEN 1 END) as count_dn,
                COUNT(CASE WHEN locations.id IS NULL OR (
                    locations.address NOT LIKE '%Hồ Chí Minh%'
                    AND locations.address NOT LIKE '%TP.HCM%'
                    AND locations.address NOT LIKE '%TP. HCM%'
                    AND locations.address NOT LIKE '%Sài Gòn%'
                    AND locations.address NOT LIKE '%Hà Nội%'
                    AND locations.address NOT LIKE '%Đà Nẵng%'
                ) THEN 1 END) as count_other
            ")
            ->first();

        $countHcm = (int) ($cityCounts->count_hcm ?? 0);
        $countHn = (int) ($cityCounts->count_hn ?? 0);
        $countDn = (int) ($cityCounts->count_dn ?? 0);
        $countOther = (int) ($cityCounts->count_other ?? 0);

        $destinations = [
            [
                'name' => 'Tp. Hồ Chí Minh',
                'query' => 'Hồ Chí Minh',
                'count' => $countHcm,
                'image' => asset('assets/images/destinations/ho-chi-minh.jpg')
            ],
            [
                'name' => 'Hà Nội',
                'query' => 'Hà Nội',
                'count' => $countHn,
                'image' => asset('assets/images/destinations/ha-noi.jpg')
            ],
            [
                'name' => 'Đà Nẵng',
                'query' => 'Đà Nẵng',
                'count' => $countDn,
                'image' => asset('assets/images/destinations/da-nang.jpg')
            ],
            [
                'name' => 'Vị trí khác',
                'query' => '',
                'location' => 'other',
                'count' => $countOther,
                'image' => asset('assets/images/destinations/khac.jpg')
            ],
        ];

        // 6. Các danh mục sự kiện cụ thể: CHỈ HIỂN THỊ DANH MỤC CÓ SỐ LƯỢNG EVENT >= 4 VÀ CHỈ LẤY 4 SỰ KIỆN
        $categorySections = Category::where('is_active', true)
            ->whereNotIn('slug', ['ve-ban-lai', 'khac'])
            ->withCount([
                'events' => function ($query) {
                    $query->where('is_published', true)->where('starts_at', '>=', now());
                }
            ])
            ->having('events_count', '>=', 4)
            ->with([
                'events' => function ($query) {
                    $query->activeListing()->upcoming()->take(4);
                }
            ])
            ->get();

        return view('home.index', compact(
            'heroEvents',
            'trendingEvents',
            'weekendEvents',
            'monthEvents',
            'resaleEvents',
            'destinations',
            'categorySections'
        ));
    }
}
