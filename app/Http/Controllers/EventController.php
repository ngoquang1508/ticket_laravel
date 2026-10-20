<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of events, optionally filtered by category slug and search term.
     */
    public function index(Request $request, ?string $categorySlug = null)
    {
        // Nếu người dùng truy cập /events/{categorySlug} nhưng lại truyền query ?categories=...,
        // để tránh xung đột đường dẫn và chuẩn hóa URL, tự động redirect sang /events?categories=...
        if ($categorySlug && $request->has('categories')) {
            return redirect()->route('events.index', $request->query());
        }

        $currentCategory = null;
        $searchTerm = trim($request->get('q', ''));
        $locationFilter = $request->get('location', 'all');
        $datePreset = $request->get('date_preset');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $isFree = $request->boolean('is_free');
        
        // Nhận danh mục từ route hoặc từ query params (mảng hoặc chuỗi phân tách bởi dấu phẩy)
        $selectedCategories = [];
        if ($request->has('categories')) {
            $rawCats = $request->get('categories');
            $selectedCategories = is_array($rawCats) ? $rawCats : array_filter(explode(',', (string) $rawCats));
        }

        $query = Event::activeListing();

        // 1. Lọc theo danh mục
        if (!empty($selectedCategories)) {
            // Lọc theo các thể loại được chọn từ bộ lọc Ticketbox
            $query->whereHas('categories', fn($q) => $q->whereIn('slug', $selectedCategories));

            // Nếu chỉ chọn duy nhất 1 thể loại, gán currentCategory để active tab tương ứng
            if (count($selectedCategories) === 1) {
                $currentCategory = Category::where('slug', reset($selectedCategories))
                    ->where('is_active', true)
                    ->first();
            }
        } elseif ($categorySlug) {
            $currentCategory = Category::where('slug', $categorySlug)
                ->where('is_active', true)
                ->firstOrFail();

            $query->whereHas('categories', fn($q) => $q->where('categories.id', $currentCategory->id));
        }

        // 2. Lọc theo Vị trí (Địa điểm)
        switch ($locationFilter) {
            case 'hcm':
                $query->whereHas('location', function ($q) {
                    $q->where('address', 'like', '%Hồ Chí Minh%')
                        ->orWhere('address', 'like', '%TP.HCM%')
                        ->orWhere('address', 'like', '%TP. HCM%')
                        ->orWhere('address', 'like', '%Sài Gòn%');
                });
                break;
            case 'hn':
                $query->whereHas('location', function ($q) {
                    $q->where('address', 'like', '%Hà Nội%');
                });
                break;
            case 'dalat':
                $query->whereHas('location', function ($q) {
                    $q->where('address', 'like', '%Đà Lạt%')
                        ->orWhere('address', 'like', '%Lâm Đồng%');
                });
                break;
            case 'danang':
                $query->whereHas('location', function ($q) {
                    $q->where('address', 'like', '%Đà Nẵng%');
                });
                break;
            case 'other':
                $query->where(function ($q) {
                    $q->whereDoesntHave('location')
                        ->orWhereHas('location', function ($locQuery) {
                            $locQuery->where('address', 'not like', '%Hồ Chí Minh%')
                                ->where('address', 'not like', '%TP.HCM%')
                                ->where('address', 'not like', '%TP. HCM%')
                                ->where('address', 'not like', '%Sài Gòn%')
                                ->where('address', 'not like', '%Hà Nội%')
                                ->where('address', 'not like', '%Đà Lạt%')
                                ->where('address', 'not like', '%Lâm Đồng%')
                                ->where('address', 'not like', '%Đà Nẵng%');
                        });
                });
                break;
            default:
                // 'all' hoặc không chọn -> Toàn quốc (không áp dụng lọc)
                break;
        }

        // 3. Lọc theo Thời gian (Preset hoặc Chọn khoảng ngày trên lịch)
        if (!empty($startDate)) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = !empty($endDate) ? \Carbon\Carbon::parse($endDate)->endOfDay() : $start->copy()->endOfDay();

            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('starts_at', '<=', $start)
                            ->where('ends_at', '>=', $end);
                    });
            });
        } elseif (!empty($datePreset) && $datePreset !== 'all') {
            switch ($datePreset) {
                case 'today':
                    $start = now()->startOfDay();
                    $end = now()->endOfDay();
                    $query->where(function ($q) use ($start, $end) {
                        $q->whereBetween('starts_at', [$start, $end])
                            ->orWhereBetween('ends_at', [$start, $end]);
                    });
                    break;
                case 'tomorrow':
                    $start = now()->addDay()->startOfDay();
                    $end = now()->addDay()->endOfDay();
                    $query->where(function ($q) use ($start, $end) {
                        $q->whereBetween('starts_at', [$start, $end])
                            ->orWhereBetween('ends_at', [$start, $end]);
                    });
                    break;
                case 'weekend':
                    $start = now()->endOfWeek()->subDays(2)->startOfDay(); // Thứ 6
                    $end = now()->endOfWeek()->endOfDay(); // Chủ nhật
                    $query->where(function ($q) use ($start, $end) {
                        $q->whereBetween('starts_at', [$start, $end])
                            ->orWhereBetween('ends_at', [$start, $end]);
                    });
                    break;
                case 'month':
                    $start = now()->startOfMonth();
                    $end = now()->endOfMonth()->endOfDay();
                    $query->where(function ($q) use ($start, $end) {
                        $q->whereBetween('starts_at', [$start, $end])
                            ->orWhereBetween('ends_at', [$start, $end]);
                    });
                    break;
            }
        }

        // 4. Lọc Giá vé Miễn phí (Sự kiện không có vé hoặc có vé giá 0đ)
        if ($isFree) {
            $query->where(function ($q) {
                $q->whereDoesntHave('ticketTypes')
                    ->orWhereHas('ticketTypes', fn($tq) => $tq->where('price', '<=', 0));
            });
        }

        // 5. Lọc theo từ khóa tìm kiếm (nếu có)
        if ($searchTerm !== '') {
            $query->search($searchTerm);
        }

        // 6. Phản hồi AJAX cho nút "Xem thêm" (Load more)
        if ($request->ajax()) {
            $events = $request->type === 'upcoming'
                ? (clone $query)->upcoming()->paginate(8, ['*'], 'page')
                : (clone $query)->past()->paginate(8, ['*'], 'page');

            $events->withQueryString();

            $html = '';
            foreach ($events as $event) {
                $html .= \Illuminate\Support\Facades\Blade::render('<x-event-card :event="$event" />', ['event' => $event]);
            }

            return response()->json([
                'html' => $html,
                'hasMorePages' => $events->hasMorePages()
            ]);
        }

        $upcomingEvents = (clone $query)->upcoming()->paginate(8, ['*'], 'upcoming_page')->withQueryString();
        $pastEvents = (clone $query)->past()->paginate(8, ['*'], 'past_page')->withQueryString();

        // Danh sách tất cả các thể loại để hiển thị trong Popover Bộ lọc
        $allCategories = Category::where('is_active', true)->orderBy('name')->get();

        return view('events.index', compact(
            'upcomingEvents',
            'pastEvents',
            'currentCategory',
            'searchTerm',
            'allCategories',
            'locationFilter',
            'datePreset',
            'startDate',
            'endDate',
            'isFree',
            'selectedCategories'
        ));
    }

    /**
     * API gợi ý tìm kiếm tức thì cho thanh search (Live Search Autocomplete)
     */
    public function searchSuggestions(Request $request)
    {
        $term = trim($request->get('q', ''));

        if (mb_strlen($term) < 1) {
            return response()->json([
                'events' => [],
                'total' => 0,
            ]);
        }

        $baseQuery = Event::activeListing()->search($term);
        $total = (clone $baseQuery)->count();

        $events = (clone $baseQuery)
            ->upcoming()
            ->take(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'slug' => $event->slug,
                    'url' => route('events.show', $event->slug),
                    'image' => $event->image ? asset('storage/' . $event->image) : null,
                    'date' => $event->starts_at ? $event->starts_at->format('d/m/Y - H:i') : 'Đang cập nhật',
                    'city' => $event->location ? ($event->location->city ?? $event->location->name) : 'Toàn quốc',
                    'price' => $event->ticket_types_min_price
                        ? number_format($event->ticket_types_min_price, 0, ',', '.') . ' đ'
                        : 'Liên hệ',
                ];
            });

        return response()->json([
            'events' => $events,
            'total' => $total,
            'search_url' => route('events.index', ['q' => $term]),
        ]);
    }

    public function show(string $slug)
    {
        $event = Event::with(['location', 'categories'])->withMin('ticketTypes', 'price')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $currentCategory = $event->categories->first();
        $categoryId = $currentCategory ? $currentCategory->id : 0;

        $relatedEvents = collect();
        if ($categoryId) {
            $relatedEvents = Event::activeListing()
                ->upcoming()
                ->where('id', '!=', $event->id)
                ->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                })
                ->orderBy('starts_at', 'asc')
                ->take(4)
                ->get();
        }

        return view('events.show', compact('event', 'currentCategory', 'relatedEvents'));
    }
}
