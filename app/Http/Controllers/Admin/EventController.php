<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::with(['location', 'categories'])
            ->withCount(['ticketTypes', 'seats'])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('location_id'), fn ($query) => $query->where('location_id', $request->integer('location_id')))
            ->when($request->filled('sale_mode'), fn ($query) => $query->where('sale_mode', $request->string('sale_mode')))
            ->when($request->filled('is_published'), fn ($query) => $query->where('is_published', $request->boolean('is_published')))
            ->when($request->filled('is_featured'), fn ($query) => $query->where('is_featured', $request->boolean('is_featured')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $locations = Location::orderBy('name')->get(['id', 'name']);

        return view('admin.events.index', compact('events', 'locations'));
    }

    public function create(): View
    {
        return view('admin.events.create', array_merge(
            ['event' => null],
            $this->formData(),
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEvent($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $categories = $validated['categories'] ?? [];
        unset($validated['categories']);

        $event = Event::create($validated);
        $event->categories()->sync($categories);

        if ($event->sale_mode === 'free_sale') {
            $this->saveFreeSaleTicket($request, $event);
        }

        return redirect()->route($event->sale_mode === 'free_sale'
            ? 'admin.events.show'
            : 'admin.events.seat-map.edit', $event)
            ->with('success', 'Đã tạo sự kiện thành công.');
    }

    public function show(Event $event): View
    {
        $event->load(['location', 'categories', 'ticketTypes', 'seatMap']);
        $event->loadCount('seats');
        $availableSeats = $event->seats()->where('status', 'available')->count();
        $soldSeats = $event->seats()->where('status', 'sold')->count();

        return view('admin.events.show', compact('event', 'availableSeats', 'soldSeats'));
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', array_merge(
            ['event' => $event->load('categories')],
            $this->formData(),
        ));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $this->validateEvent($request, $event);

        $oldSaleMode = $event->sale_mode;
        $newSaleMode = $validated['sale_mode'];

        if ($oldSaleMode !== $newSaleMode) {
            $hasSoldTickets = $event->ticketTypes()->where('sold_quantity', '>', 0)->exists();
            $hasSoldSeats = $event->seats()->where('status', 'sold')->exists();

            if ($hasSoldTickets || $hasSoldSeats) {
                return back()->withInput()->withErrors([
                    'sale_mode' => 'Không thể thay đổi hình thức bán vé vì sự kiện đã có vé hoặc ghế được bán.',
                ]);
            }
        }

        $validated['slug'] = $this->uniqueSlug($validated['name'], $event->id);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }

            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $categories = $validated['categories'] ?? [];
        unset($validated['categories']);

        $oldSaleMode = $event->sale_mode;

        $event->update($validated);
        $event->categories()->sync($categories);

        if ($oldSaleMode !== $event->sale_mode) {
            if ($event->sale_mode !== 'assigned_seat') {
                $event->seats()->delete();
            }

            foreach ($event->ticketTypes as $ticket) {
                // Remove prefixes to get the base name
                $baseName = preg_replace('/^(Ghế - (Khu )?|Khu )/', '', $ticket->name);
                
                if ($event->sale_mode === 'assigned_seat') {
                    $ticket->update(['name' => 'Ghế - Khu ' . $baseName]);
                } elseif ($event->sale_mode !== 'free_sale') {
                    $ticket->update(['name' => 'Khu ' . $baseName]);
                }
            }
        }

        if ($event->sale_mode === 'free_sale') {
            $this->saveFreeSaleTicket($request, $event);
        }

        $message = 'Đã cập nhật sự kiện thành công.';
        if ($oldSaleMode !== $event->sale_mode && $event->sale_mode !== 'free_sale') {
            $message = 'Đã cập nhật hình thức bán vé. Vui lòng bấm "Lưu seat map" để hệ thống cập nhật lại sơ đồ và ghế ngồi.';
        }

        if ($oldSaleMode !== $event->sale_mode && $event->sale_mode !== 'free_sale') {
            return redirect()->route('admin.events.seat-map.edit', $event)
                ->with('success', $message);
        }

        return redirect()->route('admin.events.show', $event)
            ->with('success', $message);
    }

    public function destroy(Event $event): RedirectResponse
    {
        DB::transaction(function () use ($event) {
            $event->ticketTypes()->delete();
            $event->seats()->delete();
            if ($event->seatMap()->exists()) {
                $event->seatMap()->delete();
            }
            $event->categories()->detach();

            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }

            $event->delete();
        });

        return redirect()->route('admin.events.index')
            ->with('success', 'Đã xóa sự kiện thành công.');
    }

    private function formData(): array
    {
        return [
            'locations' => Location::orderBy('name')->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'parentEvents' => Event::latest()->get(['id', 'name']),
        ];
    }

    private function validateEvent(Request $request, ?Event $event = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'location_id' => ['required', 'exists:locations,id'],
            'parent_event_id' => ['nullable', 'exists:events,id', 'not_in:'.($event?->id ?? '0')],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'sale_mode' => ['required', 'in:general_admission,assigned_seat,free_sale'],
            'ticket_name' => ['required_if:sale_mode,free_sale', 'nullable', 'string', 'max:100'],
            'ticket_price' => ['required_if:sale_mode,free_sale', 'nullable', 'numeric', 'min:0'],
            'ticket_quantity' => ['required_if:sale_mode,free_sale', 'nullable', 'integer', 'min:1'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
        ];

        $validated = $request->validate($rules);
        unset($validated['ticket_name'], $validated['ticket_price'], $validated['ticket_quantity']);

        return $validated;
    }

    private function saveFreeSaleTicket(Request $request, Event $event): void
    {
        DB::transaction(function () use ($request, $event): void {
            $ticket = $event->ticketTypes()->first();

            if ($ticket && ($ticket->sold_quantity > 0 || $ticket->seats()->exists())) {
                $ticket->update([
                    'name' => $request->string('ticket_name')->toString(),
                    'price' => $request->input('ticket_price'),
                    'quantity' => max((int) $request->input('ticket_quantity'), $ticket->sold_quantity),
                    'is_active' => true,
                ]);

                return;
            }

            $event->ticketTypes()->delete();
            $event->ticketTypes()->create([
                'name' => $request->string('ticket_name')->toString(),
                'price' => $request->input('ticket_price'),
                'quantity' => (int) $request->input('ticket_quantity'),
                'is_active' => true,
            ]);
        });
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'event';
        $slug = $base;
        $counter = 2;

        while (Event::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
