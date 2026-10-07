<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Seat;
use App\Models\SeatMap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SeatMapController extends Controller
{
    public function edit(Event $event): View
    {
        abort_if($event->sale_mode === 'free_sale', 404, 'Sự kiện bán tự do không sử dụng seatmap.');

        $event->load('seatMap');
        $ticketTypes = $event->ticketTypes()->orderBy('name')->get();

        return view('admin.seat-map.edit', compact('event', 'ticketTypes'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        if ($event->sale_mode === 'free_sale') {
            return redirect()->route('admin.events.show', $event)
                ->withErrors(['seat_map' => 'Sự kiện bán tự do không sử dụng seatmap.']);
        }

        $validated = $request->validate([
            'layout' => ['required', 'json'],
        ], [
            'layout.required' => 'Vui lòng nhập layout seat map.',
            'layout.json' => 'Layout phải là JSON hợp lệ.',
        ]);

        $layout = json_decode($validated['layout'], true);
        $objects = collect($layout['objects'] ?? [])
            ->filter(fn ($object) => ($object['type'] ?? null) === 'zone');
        $ticketTypeIds = [];

        DB::transaction(function () use ($event, &$layout, $objects, &$ticketTypeIds): void {
            $updatedObjects = [];

            foreach ($objects as $object) {
                $name = trim((string) ($object['name'] ?? ''));
                $quantity = (int) ($object['count'] ?? 0);
                $price = (float) ($object['price'] ?? 0);

                if ($name === '' || $quantity < 1 || $price < 0) {
                    abort(422, 'Mỗi dãy/khu cần có tên, số lượng hợp lệ và giá vé không âm.');
                }

                $ticketId = (int) ($object['ticket_type_id'] ?? 0);
                $ticket = $ticketId > 0
                    ? $event->ticketTypes()->whereKey($ticketId)->first()
                    : null;

                $ticketData = [
                    'name' => $event->sale_mode === 'assigned_seat' ? 'Ghế - Khu '.$name : 'Khu '.$name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'is_active' => true,
                ];

                if ($ticket) {
                    $ticket->update([
                        ...$ticketData,
                        'quantity' => max($quantity, $ticket->sold_quantity),
                    ]);
                } else {
                    $ticket = $event->ticketTypes()->create($ticketData);
                }

                $object['ticket_type_id'] = $ticket->id;
                $object['price'] = $price;
                $object['count'] = $quantity;
                $ticketTypeIds[] = $ticket->id;
                $updatedObjects[(string) ($object['id'] ?? '')] = $object;
            }

            $layout['objects'] = collect($layout['objects'] ?? [])
                ->map(fn ($object) => $updatedObjects[(string) ($object['id'] ?? '')] ?? $object)
                ->values()->all();
            $layout['rows'] = collect($layout['objects'])
                ->filter(fn ($object) => ($object['type'] ?? null) === 'zone')
                ->map(fn ($object) => [
                    'name' => $object['name'],
                    'seats' => (int) $object['count'],
                    'zone' => $object['zone'] ?? $object['name'],
                    'price' => (float) $object['price'],
                    'ticket_type_id' => (int) $object['ticket_type_id'],
                ])->values()->all();

            SeatMap::updateOrCreate(['event_id' => $event->id], ['layout' => $layout]);

            $event->ticketTypes()
                ->whereNotIn('id', $ticketTypeIds ?: [0])
                ->where('sold_quantity', 0)
                ->whereDoesntHave('seats')
                ->delete();

            if ($event->sale_mode === 'assigned_seat') {
                $seatMap = $event->seatMap()->first();
                $rows = $layout['rows'] ?? [];

                foreach ($rows as $row) {
                    $rowName = trim((string) ($row['name'] ?? ''));
                    $seatCount = (int) ($row['seats'] ?? 0);
                    $ticketTypeId = (int) ($row['ticket_type_id'] ?? 0);

                    if ($rowName === '' || $seatCount < 1 || ! in_array($ticketTypeId, $ticketTypeIds, true)) {
                        continue;
                    }

                    for ($number = 1; $number <= $seatCount; $number++) {
                        Seat::firstOrCreate(
                            [
                                'event_id' => $event->id,
                                'seat_number' => $rowName.$number,
                            ],
                            [
                                'seat_map_id' => $seatMap->id,
                                'ticket_type_id' => $ticketTypeId,
                                'zone' => $row['zone'] ?? null,
                                'row_name' => $rowName,
                                'status' => 'available',
                            ],
                        );
                    }
                }
            }
        });

        return redirect()->route('admin.events.seat-map.edit', $event)
            ->with('success', 'Đã lưu seat map và cập nhật ghế thành công.');
    }
}
