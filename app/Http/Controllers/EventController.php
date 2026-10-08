<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of events, optionally filtered by category slug.
     */
    public function index(Request $request, ?string $categorySlug = null)
    {
        $currentCategory = null;
        $query = Event::activeListing();

        if ($categorySlug) {
            $currentCategory = Category::where('slug', $categorySlug)
                ->where('is_active', true)
                ->firstOrFail();

            $query->whereHas('categories', fn($q) => $q->where('categories.id', $currentCategory->id));
        }

        if ($request->ajax()) {
            $events = $request->type === 'upcoming'
                ? (clone $query)->upcoming()->paginate(8, ['*'], 'page')
                : (clone $query)->past()->paginate(8, ['*'], 'page');

            $html = '';
            foreach ($events as $event) {
                $html .= \Illuminate\Support\Facades\Blade::render('<x-event-card :event="$event" />', ['event' => $event]);
            }

            return response()->json([
                'html' => $html,
                'hasMorePages' => $events->hasMorePages()
            ]);
        }

        $upcomingEvents = (clone $query)->upcoming()->paginate(8, ['*'], 'upcoming_page');
        $pastEvents = (clone $query)->past()->paginate(8, ['*'], 'past_page');

        return view('events.index', compact('upcomingEvents', 'pastEvents', 'currentCategory'));
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
