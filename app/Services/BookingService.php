<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * BookingService
 *
 * Toàn bộ logic nghiệp vụ đặt vé, thanh toán và hủy đơn tập trung tại đây.
 * Controller chỉ gọi service và xử lý HTTP response.
 */
class BookingService
{
    /**
     * Giới hạn vé tối đa mỗi lần đặt và mỗi tài khoản cho một sự kiện.
     */
    const MAX_TICKETS_PER_ORDER = 10;
    const MAX_TICKETS_PER_USER = 10;

    /**
     * Thời gian giữ vé (phút) trước khi order hết hạn.
     */
    const ORDER_EXPIRE_MINUTES = 10;

    // =========================================================================
    // PHẦN 1: TẠO ORDER (ĐẶT VÉ)
    // =========================================================================

    /**
     * Tạo order mới. Trả về Order đã tạo hoặc ném Exception.
     *
     * @param  Event  $event
     * @param  array  $input  Dữ liệu từ request (đã qua validate cơ bản)
     * @return Order
     *
     * @throws \Exception
     */
    public function createOrder(Event $event, array $input): Order
    {
        // --- Kiểm tra thời gian sự kiện ---
        $this->assertEventBookable($event);

        $isAssignedSeat = $event->sale_mode === 'assigned_seat';

        if ($isAssignedSeat) {
            return $this->createAssignedSeatOrder($event, $input);
        } else {
            return $this->createGeneralOrder($event, $input);
        }
    }

    // -------------------------------------------------------------------------
    // Tạo order kiểu ghế cố định (assigned_seat)
    // -------------------------------------------------------------------------

    /**
     * Tạo order cho sự kiện ghế cố định (assigned_seat).
     *
     * @throws \Exception
     */
    private function createAssignedSeatOrder(Event $event, array $input): Order
    {
        // Lấy danh sách seat id từ input
        $seatIds = $this->parseSeatIds($input['seats'] ?? '');

        if (empty($seatIds)) {
            throw new \Exception('Vui lòng chọn ít nhất 1 ghế.');
        }

        $count = count($seatIds);

        // Kiểm tra giới hạn vé mỗi lần đặt
        if ($count > self::MAX_TICKETS_PER_ORDER) {
            throw new \Exception('Bạn chỉ được chọn tối đa ' . self::MAX_TICKETS_PER_ORDER . ' ghế trong 1 lần đặt.');
        }

        // Kiểm tra lịch sử đặt của user cho event này
        $this->assertUserQuota($event, auth()->id(), $count);

        return DB::transaction(function () use ($event, $seatIds, $count) {
            // Khóa hàng trong DB để tránh race condition
            $lockedSeats = Seat::whereIn('seat_number', $seatIds)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->get();

            // Kiểm tra số lượng seat hợp lệ
            if ($lockedSeats->count() !== $count) {
                throw new \Exception('Ghế không hợp lệ hoặc không thuộc sự kiện này.');
            }

            // Kiểm tra tất cả ghế phải đang available
            $unavailable = $lockedSeats->where('status', '!=', 'available');
            if ($unavailable->isNotEmpty()) {
                $nums = $unavailable->pluck('seat_number')->join(', ');
                throw new \Exception("Ghế {$nums} vừa bị đặt bởi người khác. Vui lòng chọn lại.");
            }

            // Nhóm ghế theo ticket_type_id để tạo order_items
            $seatsGrouped = $lockedSeats->groupBy('ticket_type_id');

            // Lấy ticket types kèm giá từ DB (không tin frontend)
            $ticketTypeIds = $seatsGrouped->keys()->toArray();
            $ticketTypes = TicketType::where('event_id', $event->id)
                ->whereIn('id', $ticketTypeIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($seatsGrouped as $ticketTypeId => $seats) {
                $ticketType = $ticketTypes->get($ticketTypeId);
                if (!$ticketType) {
                    throw new \Exception('Loại vé không hợp lệ.');
                }

                $qty = $seats->count();
                $price = (float) $ticketType->price;
                $total = $qty * $price;

                $totalAmount += $total;

                $ticketType->decrement('quantity', $qty);

                $orderItemsData[] = [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $total,
                    'seats' => $seats, // tạm thời để tạo tickets
                    'status' => 'active',
                ];
            }

            // Tạo order
            $order = $this->buildOrder($event, $totalAmount);

            // Tạo order_items, tickets và hold ghế
            foreach ($orderItemsData as $itemData) {
                $seats = $itemData['seats'];
                unset($itemData['seats']);

                $orderItem = $order->items()->create($itemData);

                foreach ($seats as $seat) {
                    // Chuyển ghế sang held
                    $seat->update(['status' => 'held']);

                    // Tạo ticket
                    Ticket::create([
                        'order_id' => $order->id,
                        'order_item_id' => $orderItem->id,
                        'seat_id' => $seat->id,
                        'ticket_code' => $this->generateTicketCode(),
                    ]);
                }
            }

            return $order;
        });
    }

    // -------------------------------------------------------------------------
    // Tạo order kiểu chọn số lượng (general_admission / free_sale)
    // -------------------------------------------------------------------------

    /**
     * Tạo order cho sự kiện general_admission hoặc free_sale.
     *
     * @throws \Exception
     */
    private function createGeneralOrder(Event $event, array $input): Order
    {
        $ticketsInput = $input['tickets'] ?? [];

        if (empty($ticketsInput)) {
            throw new \Exception('Vui lòng chọn ít nhất 1 vé.');
        }

        // Chuẩn hoá input: bỏ qty = 0
        $ticketsInput = array_filter(
            array_map('intval', $ticketsInput),
            fn($qty) => $qty > 0
        );

        if (empty($ticketsInput)) {
            throw new \Exception('Vui lòng chọn ít nhất 1 vé.');
        }

        $totalRequested = array_sum($ticketsInput);

        if ($totalRequested > self::MAX_TICKETS_PER_ORDER) {
            throw new \Exception('Bạn chỉ được chọn tối đa ' . self::MAX_TICKETS_PER_ORDER . ' vé trong 1 lần đặt.');
        }

        // Kiểm tra lịch sử đặt của user
        $this->assertUserQuota($event, auth()->id(), $totalRequested);

        // Lấy ticket types từ DB (không tin giá frontend)
        $ticketTypeIds = array_keys($ticketsInput);
        $ticketTypes = TicketType::where('event_id', $event->id)
            ->whereIn('id', $ticketTypeIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Kiểm tra tất cả ticket type đều hợp lệ
        foreach ($ticketsInput as $ttId => $qty) {
            if (!$ticketTypes->has($ttId)) {
                throw new \Exception('Loại vé không hợp lệ hoặc đã bị vô hiệu hóa.');
            }
        }

        return DB::transaction(function () use ($event, $ticketsInput, $ticketTypes) {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($ticketsInput as $ttId => $qty) {
                // Khóa dòng ticket_type để tránh race condition
                $ticketType = TicketType::where('id', $ttId)
                    ->where('event_id', $event->id)
                    ->lockForUpdate()
                    ->first();

                if (!$ticketType) {
                    throw new \Exception('Loại vé không tồn tại.');
                }

                if (!$ticketType->is_active) {
                    throw new \Exception("Loại vé \"{$ticketType->name}\" đã bị vô hiệu hóa.");
                }

                // Số lượng thực sự còn lại = quantity (đã trừ các pending/processing)
                if ($ticketType->quantity < $qty) {
                    throw new \Exception(
                        "Vé \"{$ticketType->name}\" không đủ số lượng (chỉ còn {$ticketType->quantity} vé)."
                    );
                }

                // Giữ số lượng ngay lập tức bằng cách giảm quantity
                $ticketType->decrement('quantity', $qty);

                $price = (float) $ticketType->price;
                $total = $qty * $price;
                $totalAmount += $total;

                $orderItemsData[] = [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $total,
                    'status' => 'active',
                ];
            }

            // Tạo order
            $order = $this->buildOrder($event, $totalAmount);

            // Tạo order_items và tickets
            foreach ($orderItemsData as $itemData) {
                $orderItem = $order->items()->create($itemData);

                // Mỗi vé tạo một ticket record riêng để check-in
                for ($i = 0; $i < $itemData['quantity']; $i++) {
                    Ticket::create([
                        'order_id' => $order->id,
                        'order_item_id' => $orderItem->id,
                        'seat_id' => null,
                        'ticket_code' => $this->generateTicketCode(),
                    ]);
                }
            }

            return $order;
        });
    }

    // =========================================================================
    // PHẦN 2: XÁC NHẬN THANH TOÁN (CONFIRM ORDER)
    // =========================================================================

    /**
     * Xác nhận order sau khi thanh toán thành công.
     * Chống xử lý nhiều lần bằng cách kiểm tra và khóa trạng thái order.
     *
     * @param  Order   $order
     * @param  string  $paymentRef   Mã tham chiếu giao dịch (VNPay hoặc manual)
     * @param  string  $paymentMethod
     * @return void
     *
     * @throws \Exception  Nếu order không ở trạng thái pending hoặc đã hết hạn
     */
    public function confirmOrder(Order $order, string $paymentRef = '', string $paymentMethod = 'direct'): void
    {
        DB::transaction(function () use ($order, $paymentRef, $paymentMethod) {
            // Khóa dòng order để tránh confirm nhiều lần
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            // Kiểm tra trạng thái (chống xử lý nhiều lần)
            if ($locked->status !== 'pending') {
                throw new \Exception('Đơn hàng không ở trạng thái chờ thanh toán. Trạng thái hiện tại: ' . $locked->status);
            }

            // Kiểm tra hết hạn
            if (now() > $locked->expires_at) {
                throw new \Exception('Đơn hàng đã hết hạn thanh toán.');
            }

            $isAssignedSeat = $locked->event->sale_mode === 'assigned_seat';

            // Cập nhật order sang completed
            $locked->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'vnp_txn_ref' => $paymentRef ?: null,
            ]);

            // Cập nhật order_items sang completed
            $locked->items()->update(['status' => 'completed']);

            // Xử lý ghế và sold_quantity
            foreach ($locked->items as $item) {
                // Cập nhật sold_quantity (KHÔNG cộng khi pending, chỉ cộng khi confirmed)
                TicketType::where('id', $item->ticket_type_id)
                    ->increment('sold_quantity', $item->quantity);

                if ($isAssignedSeat) {
                    // Chuyển ghế từ held → sold
                    $ticketIds = Ticket::where('order_item_id', $item->id)
                        ->whereNotNull('seat_id')
                        ->pluck('seat_id');

                    Seat::whereIn('id', $ticketIds)->update(['status' => 'sold']);
                }
            }
        });

        // Gửi email thông báo
        \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderConfirmed($order));
    }

    /**
     * Xác nhận thanh toán trực tiếp (direct payment – admin xác nhận thủ công).
     * Đặt status = processing, chờ admin confirm thực sự sau.
     *
     * @param  Order  $order
     * @return void
     */
    public function markAsProcessing(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($locked->status !== 'pending') {
                throw new \Exception('Đơn hàng không ở trạng thái chờ thanh toán.');
            }

            if (now() > $locked->expires_at) {
                throw new \Exception('Đơn hàng đã hết hạn thanh toán.');
            }

            // Với direct payment: admin sẽ xác nhận sau, cập nhật processing
            // sold_quantity và trạng thái ghế chỉ thay đổi khi admin confirm thật sự
            $locked->update([
                'status' => 'processing',
                'payment_method' => 'direct',
            ]);

            // items vẫn active, chờ admin confirm
        });
    }

    /**
     * Admin xác nhận đơn processing (thanh toán tiền mặt/chuyển khoản).
     * Đây là bước thực sự confirm sau khi đã nhận tiền.
     *
     * @param  Order  $order
     * @return void
     */
    public function adminConfirmOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($locked->status !== 'processing') {
                throw new \Exception('Đơn hàng không ở trạng thái đang xử lý.');
            }

            $isAssignedSeat = $locked->event->sale_mode === 'assigned_seat';

            $locked->update([
                'status' => 'completed',
                'payment_status' => 'paid',
            ]);

            $locked->items()->update(['status' => 'completed']);

            foreach ($locked->items as $item) {
                TicketType::where('id', $item->ticket_type_id)
                    ->increment('sold_quantity', $item->quantity);

                if ($isAssignedSeat) {
                    $ticketIds = Ticket::where('order_item_id', $item->id)
                        ->whereNotNull('seat_id')
                        ->pluck('seat_id');

                    Seat::whereIn('id', $ticketIds)->update(['status' => 'sold']);
                }
            }
        });

        // Gửi email thông báo
        \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderConfirmed($order));
    }

    // =========================================================================
    // PHẦN 3: HỦY / HẾT HẠN ORDER
    // =========================================================================

    /**
     * Hủy order (hết hạn hoặc người dùng/admin hủy).
     * Chống hủy nhiều lần bằng kiểm tra trạng thái.
     *
     * @param  Order   $order
     * @param  string  $newStatus   'expired' hoặc 'cancelled'
     * @return void
     */
    public function cancelOrder(Order $order, string $newStatus = 'expired'): void
    {
        DB::transaction(function () use ($order, $newStatus) {
            // Khóa dòng order để tránh cancel nhiều lần
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            // Chỉ cho phép cancel khi order đang pending hoặc processing
            if (!in_array($locked->status, ['pending', 'processing'])) {
                // Nếu đã expired/cancelled/completed → không làm gì
                return;
            }

            $isAssignedSeat = $locked->event->sale_mode === 'assigned_seat';

            // Cập nhật trạng thái order
            $locked->update(['status' => $newStatus]);

            // Cập nhật order_items sang cancelled (không xóa để giữ lịch sử)
            $locked->items()->update(['status' => 'cancelled']);

            // Giải phóng ghế (assigned_seat)
            $tickets = Ticket::where('order_id', $locked->id)->get();
            foreach ($tickets as $ticket) {
                if ($ticket->seat_id) {
                    Seat::where('id', $ticket->seat_id)->update(['status' => 'available']);
                }
            }

            // Hoàn lại số lượng vé (cho cả assigned_seat và general_admission)
            foreach ($locked->items as $item) {
                // Chỉ hoàn lại nếu item còn active (chưa cancelled trước đó)
                // Lưu ý: items() đã load, nhưng status vừa bị update ở trên
                // Hoàn lại dù sao vì chúng ta đã kiểm tra order status ở đầu
                TicketType::where('id', $item->ticket_type_id)
                    ->increment('quantity', $item->quantity);
            }

            // KHÔNG xóa Order, OrderItem, Ticket để giữ lịch sử
        });
    }

    // =========================================================================
    // PHẦN 4: HELPERS
    // =========================================================================

    /**
     * Kiểm tra sự kiện có thể đặt vé không.
     * Chặn khi đã kết thúc.
     *
     * @throws \Exception
     */
    public function assertEventBookable(Event $event): void
    {
        if (now() > $event->ends_at) {
            throw new \Exception('Sự kiện này đã kết thúc, không thể đặt vé.');
        }

        if (!$event->is_published) {
            throw new \Exception('Sự kiện này chưa được công bố.');
        }
    }

    /**
     * Kiểm tra quota vé của user cho event này.
     *
     * @throws \Exception
     */
    private function assertUserQuota(Event $event, int $userId, int $requestedQty): void
    {
        // Đếm vé đã đặt thành công của user (pending/processing/completed)
        $alreadyBooked = OrderItem::whereHas('order', function ($q) use ($event, $userId) {
            $q->where('user_id', $userId)
                ->where('event_id', $event->id)
                ->whereIn('status', ['pending', 'processing', 'completed']);
        })->where('status', '!=', 'cancelled')
            ->sum('quantity');

        if ($alreadyBooked + $requestedQty > self::MAX_TICKETS_PER_USER) {
            $remaining = max(0, self::MAX_TICKETS_PER_USER - $alreadyBooked);
            throw new \Exception(
                "Bạn đã đặt {$alreadyBooked} vé trước đó. " .
                "Mỗi tài khoản chỉ được đặt tối đa " . self::MAX_TICKETS_PER_USER . " vé cho một sự kiện. " .
                "Bạn chỉ còn có thể đặt thêm {$remaining} vé."
            );
        }
    }

    /**
     * Tạo bản ghi Order với thông tin chung.
     */
    private function buildOrder(Event $event, float $totalAmount): Order
    {
        $user = auth()->user();

        return Order::create([
            'order_code' => 'ORD' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? '',
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => 'direct', // mặc định, cập nhật khi checkout
            'expires_at' => now()->addMinutes(self::ORDER_EXPIRE_MINUTES),
        ]);
    }

    /**
     * Parse seat IDs từ chuỗi "1,2,3" thành array integer.
     */
    private function parseSeatIds(string $input): array
    {
        return array_filter(
            array_map('trim', explode(',', $input)),
            fn($id) => !empty($id)
        );
    }

    /**
     * Tạo mã vé ngẫu nhiên duy nhất.
     */
    private function generateTicketCode(): string
    {
        do {
            $code = 'TKT' . strtoupper(Str::random(10));
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
