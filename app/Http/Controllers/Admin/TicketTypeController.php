<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    public function index(Event $event): View
    {
        $ticketTypes = $event->ticketTypes()->latest()->paginate(10);
        return view('admin.ticket-types.index', compact('event', 'ticketTypes'));
    }

    public function create(Event $event): View
    {
        return view('admin.ticket-types.create', compact('event'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $this->validateTicketType($request);
        $validated['is_active'] = $request->boolean('is_active');
        $event->ticketTypes()->create($validated);
        return redirect()->route('admin.events.ticket-types.index', $event)->with('success', 'Đã tạo loại vé thành công.');
    }

    public function edit(Event $event, TicketType $ticketType): View
    {
        abort_unless($ticketType->event_id === $event->id, 404);
        return view('admin.ticket-types.edit', compact('event', 'ticketType'));
    }

    public function update(Request $request, Event $event, TicketType $ticketType): RedirectResponse
    {
        abort_unless($ticketType->event_id === $event->id, 404);
        $validated = $this->validateTicketType($request, $ticketType);
        $validated['is_active'] = $request->boolean('is_active');
        $ticketType->update($validated);
        return redirect()->route('admin.events.ticket-types.index', $event)->with('success', 'Đã cập nhật loại vé thành công.');
    }

    public function destroy(Event $event, TicketType $ticketType): RedirectResponse
    {
        abort_unless($ticketType->event_id === $event->id, 404);
        if ($ticketType->sold_quantity > 0 || $ticketType->seats()->exists()) {
            return back()->withErrors(['ticket_type' => 'Không thể xóa loại vé đã có vé bán hoặc seat liên quan.']);
        }
        $ticketType->delete();
        return redirect()->route('admin.events.ticket-types.index', $event)->with('success', 'Đã xóa loại vé thành công.');
    }

    private function validateTicketType(Request $request, ?TicketType $ticketType = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
        if ($ticketType) {
            $rules['quantity'][] = 'min:'.$ticketType->sold_quantity;
        }
        return $request->validate($rules);
    }
}
