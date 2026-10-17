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
        $currentCategory = null;
        $searchTerm = trim($request->get('q', ''));
        $query = Event::activeListing();

        // 1. Lọc theo danh mục nếu có
        if ($categorySlug) {
            $currentCategory = Category::where('slug', $categorySlug)
                ->where('is_active', true)
                ->firstOrFail();

            $query->whereHas('categories', fn($q) => $q->where('categories.id', $currentCategory->id));
        }

        // 2. Lọc theo từ khóa tìm kiếm nếu có
        if ($searchTerm !== '') {
            $query->search($searchTerm);
        }

        // 3. Phản hồi AJAX cho nút "Xem thêm" (Load more)
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

        return view('events.index', compact('upcomingEvents', 'pastEvents', 'currentCategory', 'searchTerm'));
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
