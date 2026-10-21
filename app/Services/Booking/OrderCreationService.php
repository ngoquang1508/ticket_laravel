<?php

namespace App\Services\Booking;

use App\Constants\BookingConstant;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * OrderCreationService
 *
 * Chịu trách nhiệm tạo đơn hàng giữ vé (ghế cố định hoặc vé tự do)
 * và kiểm tra các ràng buộc: hạn sự kiện, giới hạn quota người dùng, tồn kho ghế/vé.
 */
class OrderCreationService
{
    public const MAX_TICKETS_PER_ORDER = BookingConstant::MAX_TICKETS_PER_ORDER;
    public const MAX_TICKETS_PER_USER = BookingConstant::MAX_TICKETS_PER_USER;
    public const ORDER_EXPIRE_MINUTES = BookingConstant::ORDER_EXPIRE_MINUTES;

    /**
     * Lấy đơn hàng pending của user cho sự kiện cụ thể nếu còn trong thời gian giữ vé.
     */
    public function getActivePendingOrder(Event $event, int $userId): ?Order
    {
        return Order::where('event_id', $event->id)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Tạo order mới.
     *
     * @throws \Exception
     */
    public function createOrder(Event $event, array $input): Order
    {
        // Kiểm tra thời gian sự kiện
        $this->assertEventBookable($event);

        $isAssignedSeat = $event->sale_mode === 'assigned_seat';

        if ($isAssignedSeat) {
            return $this->createAssignedSeatOrder($event, $input);
        } else {
            return $this->createGeneralOrder($event, $input);
        }
    }

    /**
     * Tạo order cho sự kiện ghế cố định (assigned_seat).
     *
     * @throws \Exception
     */
    public function createAssignedSeatOrder(Event $event, array $input): Order
    {
        $seatIds = $this->parseSeatIds($input['seats'] ?? '');

        if (empty($seatIds)) {
            throw new \Exception('Vui lòng chọn ít nhất 1 ghế.');
        }

        $count = count($seatIds);

        if ($count > self::MAX_TICKETS_PER_ORDER) {
            throw new \Exception('Bạn chỉ được chọn tối đa ' . self::MAX_TICKETS_PER_ORDER . ' ghế trong 1 lần đặt.');
        }

        $this->assertUserQuota($event, auth()->id(), $count);

        return DB::transaction(function () use ($event, $seatIds, $count) {
            $lockedSeats = Seat::whereIn('seat_number', $seatIds)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->get();

            if ($lockedSeats->count() !== $count) {
                throw new \Exception('Ghế không hợp lệ hoặc không thuộc sự kiện này.');
            }

            $unavailable = $lockedSeats->where('status', '!=', 'available');
            if ($unavailable->isNotEmpty()) {
                $nums = $unavailable->pluck('seat_number')->join(', ');
                throw new \Exception("Ghế {$nums} vừa bị đặt bởi người khác. Vui lòng chọn lại.");
            }

            $seatsGrouped = $lockedSeats->groupBy('ticket_type_id');
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
                    'seats' => $seats,
                    'status' => 'active',
                ];
            }

            $order = $this->buildOrder($event, $totalAmount);

            foreach ($orderItemsData as $itemData) {
                $seats = $itemData['seats'];
                unset($itemData['seats']);

                $orderItem = $order->items()->create($itemData);

                foreach ($seats as $seat) {
                    $seat->update(['status' => 'held']);

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

    /**
     * Tạo order cho sự kiện general_admission hoặc free_sale.
     *
     * @throws \Exception
     */
    public function createGeneralOrder(Event $event, array $input): Order
    {
        $ticketsInput = $input['tickets'] ?? [];

        if (empty($ticketsInput)) {
            throw new \Exception('Vui lòng chọn ít nhất 1 vé.');
        }

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

        $this->assertUserQuota($event, auth()->id(), $totalRequested);

        $ticketTypeIds = array_keys($ticketsInput);
        $ticketTypes = TicketType::where('event_id', $event->id)
            ->whereIn('id', $ticketTypeIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        foreach ($ticketsInput as $ttId => $qty) {
            if (!$ticketTypes->has($ttId)) {
                throw new \Exception('Loại vé không hợp lệ hoặc đã bị vô hiệu hóa.');
            }
        }

        return DB::transaction(function () use ($event, $ticketsInput) {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($ticketsInput as $ttId => $qty) {
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

                if ($ticketType->quantity < $qty) {
                    throw new \Exception(
                        "Vé \"{$ticketType->name}\" không đủ số lượng (chỉ còn {$ticketType->quantity} vé)."
                    );
                }

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

            $order = $this->buildOrder($event, $totalAmount);

            foreach ($orderItemsData as $itemData) {
                $orderItem = $order->items()->create($itemData);

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

    /**
     * Kiểm tra sự kiện có thể đặt vé không.
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

        if (!$event->isSaleStarted()) {
            throw new \Exception(
                'Sự kiện chưa đến thời gian mở bán vé (Dự kiến mở bán lúc: ' .
                $event->ticket_sale_starts_at->format('H:i, d/m/Y') . ').'
            );
        }
    }

    /**
     * Kiểm tra quota vé của user cho event này.
     *
     * @throws \Exception
     */
    public function assertUserQuota(Event $event, int $userId, int $requestedQty): void
    {
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
     * Lấy số vé tối đa user còn có thể mua cho sự kiện này.
     */
    public function getUserRemainingQuota(Event $event, ?int $userId): int
    {
        if (!$userId) {
            return self::MAX_TICKETS_PER_USER;
        }

        $alreadyBooked = OrderItem::whereHas('order', function ($q) use ($event, $userId) {
            $q->where('user_id', $userId)
                ->where('event_id', $event->id)
                ->whereIn('status', ['pending', 'processing', 'completed']);
        })->where('status', '!=', 'cancelled')
            ->sum('quantity');

        return max(0, self::MAX_TICKETS_PER_USER - $alreadyBooked);
    }

    /**
     * Tạo bản ghi Order với thông tin cơ bản.
     */
    public function buildOrder(Event $event, float $totalAmount): Order
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
            'payment_method' => null,
            'expires_at' => now()->addMinutes(self::ORDER_EXPIRE_MINUTES),
        ]);
    }

    /**
     * Parse chuỗi seat IDs từ request thành mảng.
     */
    public function parseSeatIds(string $input): array
    {
        return array_filter(
            array_map('trim', explode(',', $input)),
            fn($id) => !empty($id)
        );
    }

    /**
     * Tạo mã vé ngẫu nhiên duy nhất.
     */
    public function generateTicketCode(): string
    {
        do {
            $code = 'TKT' . strtoupper(Str::random(10));
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
