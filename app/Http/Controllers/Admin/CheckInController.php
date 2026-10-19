<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    /**
     * Màn hình chính quét mã QR và nhập mã vé check-in.
     */
    public function index()
    {
        // Danh sách các sự kiện đang hoạt động để Admin chọn trước khi check-in
        $events = Event::with('location')
            ->where('is_published', true)
            ->orderBy('starts_at', 'asc')
            ->get();

        // Thống kê số lượng vé đã check-in trong ngày hôm nay
        $todayCount = Ticket::whereDate('checked_in_at', today())->count();

        // 10 vé vừa check-in gần nhất trong hệ thống
        $recentCheckins = Ticket::with([
            'order.user',
            'order.event',
            'orderItem.ticketType',
            'seat',
            'checkedInBy'
        ])
            ->whereNotNull('checked_in_at')
            ->orderBy('checked_in_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.check-in.index', compact('events', 'todayCount', 'recentCheckins'));
    }

    /**
     * Kiểm tra trạng thái và thông tin của vé khi quét QR hoặc nhập mã thủ công.
     * (Không tự động check-in tại bước này).
     */
    public function verify(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'event_id' => 'required|integer|exists:events,id',
        ]);

        $ticketCode = trim($request->input('ticket_code'));
        $eventId = (int) $request->input('event_id');

        $ticket = Ticket::with([
            'order.user',
            'order.event.location',
            'orderItem.ticketType',
            'seat',
            'checkedInBy'
        ])->where('ticket_code', $ticketCode)->first();

        // Sử dụng switch-case để kiểm tra toàn bộ các điều kiện hợp lệ của vé
        switch (true) {
            // 1. Không tìm thấy vé trong hệ thống
            case !$ticket:
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Không tìm thấy vé có mã "' . $ticketCode . '" trong hệ thống!',
                ], 404);

            // 2. Vé không thuộc đúng sự kiện đang chọn check-in
            case $ticket->order?->event_id !== $eventId:
                $ticketEventName = $ticket->order?->event?->name ?? 'Khác';
                $selectedEvent = Event::find($eventId);
                $selectedEventName = $selectedEvent?->name ?? 'Sự kiện đang chọn';

                return response()->json([
                    'status' => 'wrong_event',
                    'message' => 'Vé này thuộc sự kiện "' . $ticketEventName . '", không khớp với sự kiện bạn đang chọn check-in ("' . $selectedEventName . '")!',
                    'ticket' => $this->formatTicketData($ticket),
                ], 422);

            // 3. Đơn hàng của vé chưa thanh toán thành công hoặc đã bị hủy
            case !$ticket->order || $ticket->order->status !== Order::STATUS_COMPLETED:
                switch ($ticket->order?->status) {
                    case Order::STATUS_PENDING:
                        $statusText = 'Chờ thanh toán';
                        break;
                    case Order::STATUS_PROCESSING:
                        $statusText = 'Chờ duyệt';
                        break;
                    case Order::STATUS_CANCELLED:
                        $statusText = 'Đã hủy';
                        break;
                    case Order::STATUS_EXPIRED:
                        $statusText = 'Đã hết hạn';
                        break;
                    default:
                        $statusText = 'Không hợp lệ';
                        break;
                }

                return response()->json([
                    'status' => 'invalid_order',
                    'message' => 'Đơn hàng của vé này chưa hoàn tất thanh toán (Trạng thái đơn: ' . $statusText . '). Không được phép check-in!',
                    'ticket' => $this->formatTicketData($ticket),
                ], 422);

            // 4. Vé đã được check-in trước đó
            case $ticket->is_used || $ticket->checked_in_at:
                $timeStr = $ticket->checked_in_at ? $ticket->checked_in_at->format('H:i:s, d/m/Y') : ($ticket->used_at ? $ticket->used_at->format('H:i:s, d/m/Y') : 'trước đó');
                $byName = $ticket->checkedInBy?->name ?? 'Admin';

                return response()->json([
                    'status' => 'already_used',
                    'message' => 'Vé này đã được check-in vào lúc ' . $timeStr . ' (Người thực hiện: ' . $byName . ')!',
                    'ticket' => $this->formatTicketData($ticket),
                ], 200);

            // 5. Vé hoàn toàn hợp lệ, sẵn sàng để xác nhận check-in
            default:
                return response()->json([
                    'status' => 'valid',
                    'message' => 'Vé hợp lệ! Vui lòng xác nhận check-in.',
                    'ticket' => $this->formatTicketData($ticket),
                ], 200);
        }
    }

    /**
     * Xác nhận check-in vé (chỉ cập nhật sau khi admin bấm nút xác nhận).
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'event_id' => 'required|integer|exists:events,id',
        ]);

        $ticketCode = trim($request->input('ticket_code'));
        $eventId = (int) $request->input('event_id');

        return DB::transaction(function () use ($ticketCode, $eventId) {
            $ticket = Ticket::where('ticket_code', $ticketCode)
                ->lockForUpdate()
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy vé trong hệ thống!',
                ], 404);
            }

            // Kiểm tra sự kiện
            if ($ticket->order?->event_id !== $eventId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vé không thuộc sự kiện bạn đang chọn check-in!',
                ], 422);
            }

            // Kiểm tra tránh race condition nếu đã check-in
            if ($ticket->is_used || $ticket->checked_in_at) {
                $timeStr = $ticket->checked_in_at ? $ticket->checked_in_at->format('H:i d/m/Y') : 'trước đó';
                return response()->json([
                    'success' => false,
                    'message' => 'Vé này vừa được check-in vào lúc ' . $timeStr . '!',
                ], 422);
            }

            if (!$ticket->order || $ticket->order->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng của vé chưa thanh toán thành công!',
                ], 422);
            }

            // Cập nhật trạng thái check-in
            $ticket->update([
                'is_used' => true,
                'used_at' => now(),
                'checked_in_at' => now(),
                'checked_in_by' => auth()->id(),
            ]);

            $ticket->load([
                'order.user',
                'order.event.location',
                'orderItem.ticketType',
                'seat',
                'checkedInBy'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận check-in vé thành công!',
                'ticket' => $this->formatTicketData($ticket),
            ]);
        });
    }

    /**
     * Chuẩn hóa dữ liệu vé trả về cho giao diện Frontend.
     */
    private function formatTicketData(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'is_used' => (bool) $ticket->is_used,
            'checked_in_at' => $ticket->checked_in_at ? $ticket->checked_in_at->format('H:i:s, d/m/Y') : ($ticket->used_at ? $ticket->used_at->format('H:i:s, d/m/Y') : null),
            'checked_in_by_name' => $ticket->checkedInBy?->name ?? 'Admin',
            'order_code' => $ticket->order?->order_code ?? '',
            'order_status' => $ticket->order?->status ?? '',
            'customer_name' => $ticket->order?->customer_name ?: ($ticket->order?->user?->name ?? 'Không rõ'),
            'customer_email' => $ticket->order?->customer_email ?: ($ticket->order?->user?->email ?? ''),
            'customer_phone' => $ticket->order?->customer_phone ?: ($ticket->order?->user?->phone ?? ''),
            'event_name' => $ticket->order?->event?->name ?? 'Không có thông tin sự kiện',
            'event_time' => $ticket->order?->event?->starts_at ? $ticket->order->event->starts_at->format('H:i, d/m/Y') : '',
            'event_location' => $ticket->order?->event?->location?->name ?? '',
            'event_address' => $ticket->order?->event?->location?->address ?? '',
            'ticket_type_name' => $ticket->orderItem?->ticketType?->name ?? 'Vé tham dự',
            'seat_number' => $ticket->seat?->seat_number,
            'zone' => $ticket->seat?->zone,
            'price_formatted' => number_format($ticket->orderItem?->price ?? 0, 0, ',', '.') . ' đ',
        ];
    }
}
