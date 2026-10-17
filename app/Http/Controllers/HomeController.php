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
        // 1. Hero Banner Slider (Chỉ lấy đúng 3 sự kiện nổi bật nhất)
        $heroEvents = Event::activeListing()
            ->where('is_featured', true)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc')
            ->take(3)
            ->get();

        // Fallback nếu chưa đủ 3 sự kiện nổi bật
        if ($heroEvents->count() < 3) {
            $extraEvents = Event::activeListing()
                ->where('starts_at', '>=', now())
                ->whereNotIn('id', $heroEvents->pluck('id'))
                ->orderBy('starts_at', 'asc')
                ->take(3 - $heroEvents->count())
                ->get();
            $heroEvents = $heroEvents->merge($extraEvents);
        }

        // 2. Sự kiện xu hướng (Lấy đúng 10 sự kiện bán chạy nhất)
        $trendingEvents = Event::activeListing()
            ->upcoming()
            ->withCount('orders')
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
                ->whereHas('categories', fn($q) => $q->where('categories.id', $resaleCategory->id))
                ->take(8)
                ->get();
        }
        if ($resaleEvents->isEmpty()) {
            $resaleEvents = Event::activeListing()->upcoming()->take(8)->get();
        }

        // 5. Điểm đến thú vị (3 thành phố chính: TP.HCM, Hà Nội, Đà Nẵng và Vị trí khác)
        $allActiveEvents = Event::activeListing()->upcoming()->get();

        $countHcm = $allActiveEvents->filter(fn($e) => $e->location && $e->location->city === 'TP.HCM')->count();
        $countHn = $allActiveEvents->filter(fn($e) => $e->location && $e->location->city === 'Hà Nội')->count();
        $countDn = $allActiveEvents->filter(fn($e) => $e->location && $e->location->city === 'Đà Nẵng')->count();
        $countOther = $allActiveEvents->filter(fn($e) => !$e->location || !in_array($e->location->city, ['TP.HCM', 'Hà Nội', 'Đà Nẵng']))->count();

        $destinations = [
            [
                'name' => 'Tp. Hồ Chí Minh',
                'query' => 'Hồ Chí Minh',
                'count' => $countHcm,
                'image' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'Hà Nội',
                'query' => 'Hà Nội',
                'count' => $countHn,
                'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'Đà Nẵng',
                'query' => 'Đà Nẵng',
                'count' => $countDn,
                'image' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'name' => 'Vị trí khác',
                'query' => '',
                'count' => $countOther,
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        // 6. Các danh mục sự kiện cụ thể: CHỈ HIỂN THỊ DANH MỤC CÓ SỐ LƯỢNG EVENT >= 4 VÀ CHỈ LẤY 4 SỰ KIỆN
        $categorySections = Category::where('is_active', true)
            ->whereNotIn('slug', ['ve-ban-lai', 'khac'])
            ->withCount(['events' => function ($query) {
                $query->where('is_published', true)->where('starts_at', '>=', now());
            }])
            ->having('events_count', '>=', 4)
            ->with(['events' => function ($query) {
                $query->activeListing()->upcoming()->take(4);
            }])
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
