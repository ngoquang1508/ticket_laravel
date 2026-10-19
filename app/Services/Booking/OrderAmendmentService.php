<?php

namespace App\Services\Booking;

use App\Constants\BookingConstant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;

/**
 * OrderAmendmentService
 *
 * Chịu trách nhiệm cho phép người dùng thay đổi số lượng vé (General Admission)
 * hoặc đổi danh sách ghế ngồi (Assigned Seat) trong khi đơn hàng vẫn còn thời gian giữ vé (pending).
 */
class OrderAmendmentService
{
    protected OrderCreationService $creationService;
    protected OrderCancellationService $cancellationService;

    public function __construct(
        OrderCreationService $creationService,
        OrderCancellationService $cancellationService
    ) {
        $this->creationService = $creationService;
        $this->cancellationService = $cancellationService;
    }

    /**
     * Chỉnh sửa số lượng vé cho đơn hàng ở trạng thái Pending.
     *
     * @throws \Exception
     */
    public function updateOrderTickets(Order $order, array $quantities): Order
    {
        return DB::transaction(function () use ($order, $quantities) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$lockedOrder || $lockedOrder->status !== 'pending') {
                throw new \Exception('Chỉ có thể chỉnh sửa số lượng vé cho đơn hàng đang chờ xử lý.');
            }

            if (now() > $lockedOrder->expires_at) {
                $this->cancellationService->cancelOrder($lockedOrder, 'cancelled', 'Hết hạn thanh toán');
                throw new \Exception('Đơn hàng đã hết thời gian giữ vé và đã bị hủy tự động.');
            }

            if ($lockedOrder->event->sale_mode === 'assigned_seat') {
                throw new \Exception('Sự kiện chọn ghế theo sơ đồ không hỗ trợ sửa số lượng vé trực tiếp.');
            }

            $sanitizedQuantities = [];
            foreach ($quantities as $ttId => $qty) {
                $sanitizedQuantities[(int)$ttId] = max(0, (int)$qty);
            }

            $totalRequested = array_sum($sanitizedQuantities);

            if ($totalRequested <= 0) {
                throw new \Exception('Đơn hàng phải có ít nhất 1 vé. Nếu muốn hủy toàn bộ, vui lòng bấm "Hủy đơn hàng".');
            }

            if ($totalRequested > BookingConstant::MAX_TICKETS_PER_ORDER) {
                throw new \Exception('Bạn chỉ được chọn tối đa ' . BookingConstant::MAX_TICKETS_PER_ORDER . ' vé trong một đơn hàng.');
            }

            $currentTotal = $lockedOrder->items()->where('status', 'active')->sum('quantity');
            $increase = $totalRequested - $currentTotal;
            if ($increase > 0) {
                $this->creationService->assertUserQuota($lockedOrder->event, $lockedOrder->user_id, $increase);
            }

            $newTotalAmount = 0;

            foreach ($sanitizedQuantities as $ttId => $newQty) {
                $ticketType = TicketType::where('id', $ttId)
                    ->where('event_id', $lockedOrder->event_id)
                    ->lockForUpdate()
                    ->first();

                if (!$ticketType) {
                    continue;
                }

                $orderItem = OrderItem::where('order_id', $lockedOrder->id)
                    ->where('ticket_type_id', $ttId)
                    ->first();

                $oldQty = $orderItem ? $orderItem->quantity : 0;
                $diff = $newQty - $oldQty;

                if ($diff > 0) {
                    if ($ticketType->quantity < $diff) {
                        throw new \Exception("Loại vé \"{$ticketType->name}\" không đủ số lượng (chỉ còn thêm {$ticketType->quantity} vé).");
                    }

                    $ticketType->decrement('quantity', $diff);

                    if (!$orderItem) {
                        $orderItem = $lockedOrder->items()->create([
                            'ticket_type_id' => $ticketType->id,
                            'quantity' => $newQty,
                            'price' => (float) $ticketType->price,
                            'total' => $newQty * (float) $ticketType->price,
                            'status' => 'active',
                        ]);
                    } else {
                        $orderItem->update([
                            'quantity' => $newQty,
                            'total' => $newQty * $orderItem->price,
                            'status' => 'active',
                        ]);
                    }

                    for ($i = 0; $i < $diff; $i++) {
                        Ticket::create([
                            'order_id' => $lockedOrder->id,
                            'order_item_id' => $orderItem->id,
                            'seat_id' => null,
                            'ticket_code' => $this->creationService->generateTicketCode(),
                        ]);
                    }
                } elseif ($diff < 0) {
                    $returnCount = abs($diff);
                    $ticketType->increment('quantity', $returnCount);

                    if ($newQty === 0) {
                        Ticket::where('order_id', $lockedOrder->id)
                            ->where('order_item_id', $orderItem->id)
                            ->delete();
                        $orderItem->delete();
                    } else {
                        $orderItem->update([
                            'quantity' => $newQty,
                            'total' => $newQty * $orderItem->price,
                        ]);

                        $ticketIdsToDelete = Ticket::where('order_id', $lockedOrder->id)
                            ->where('order_item_id', $orderItem->id)
                            ->limit($returnCount)
                            ->pluck('id');
                        Ticket::whereIn('id', $ticketIdsToDelete)->delete();
                    }
                }

                if ($newQty > 0 && $orderItem) {
                    $newTotalAmount += $newQty * (float) $orderItem->price;
                }
            }

            $lockedOrder->update(['total_amount' => $newTotalAmount]);

            return $lockedOrder;
        });
    }

    /**
     * Cập nhật danh sách ghế ngồi cho đơn hàng pending của sự kiện assigned_seat.
     *
     * @throws \Exception
     */
    public function updateAssignedSeatOrder(Order $order, array $newSeatNumbers): Order
    {
        if ($order->status !== 'pending') {
            throw new \Exception('Chỉ có thể chỉnh sửa ghế cho đơn hàng đang chờ xử lý.');
        }

        if ($order->expires_at && now()->greaterThan($order->expires_at)) {
            $this->cancellationService->cancelOrder($order, 'cancelled', 'Hết hạn thanh toán');
            throw new \Exception('Đơn hàng đã hết thời gian giữ vé và đã bị hủy tự động.');
        }

        if ($order->event->sale_mode !== 'assigned_seat') {
            throw new \Exception('Chức năng này chỉ áp dụng cho sự kiện có sơ đồ chọn ghế.');
        }

        $newSeatNumbers = array_values(array_unique(array_filter(array_map('trim', $newSeatNumbers))));
        $count = count($newSeatNumbers);

        if ($count <= 0) {
            throw new \Exception('Vui lòng chọn ít nhất 1 ghế. Nếu không muốn đặt vé nữa, bạn có thể hủy đơn hàng.');
        }

        if ($count > BookingConstant::MAX_TICKETS_PER_ORDER) {
            throw new \Exception('Bạn chỉ được chọn tối đa ' . BookingConstant::MAX_TICKETS_PER_ORDER . ' vé trong một đơn hàng.');
        }

        return DB::transaction(function () use ($order, $newSeatNumbers, $count) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$lockedOrder || $lockedOrder->status !== 'pending') {
                throw new \Exception('Trạng thái đơn hàng không hợp lệ.');
            }

            $currentTickets = Ticket::with('seat')
                ->where('order_id', $lockedOrder->id)
                ->get();
            $oldSeatNumbers = $currentTickets->pluck('seat.seat_number')->filter()->values()->toArray();

            $increase = $count - count($oldSeatNumbers);
            if ($increase > 0) {
                $this->creationService->assertUserQuota($lockedOrder->event, $lockedOrder->user_id, $increase);
            }

            $seatsToRemove = array_diff($oldSeatNumbers, $newSeatNumbers);
            $seatsToAdd = array_diff($newSeatNumbers, $oldSeatNumbers);

            // 1. Giải phóng các ghế bị loại bỏ
            if (!empty($seatsToRemove)) {
                $removedSeats = Seat::whereIn('seat_number', $seatsToRemove)
                    ->where('event_id', $lockedOrder->event_id)
                    ->lockForUpdate()
                    ->get();

                foreach ($removedSeats as $seat) {
                    $seat->update(['status' => 'available']);
                    if ($seat->ticket_type_id) {
                        TicketType::where('id', $seat->ticket_type_id)->increment('quantity', 1);
                    }
                    Ticket::where('order_id', $lockedOrder->id)
                        ->where('seat_id', $seat->id)
                        ->delete();
                }
            }

            // 2. Giữ các ghế mới thêm
            if (!empty($seatsToAdd)) {
                $addedSeats = Seat::whereIn('seat_number', $seatsToAdd)
                    ->where('event_id', $lockedOrder->event_id)
                    ->lockForUpdate()
                    ->get();

                if ($addedSeats->count() !== count($seatsToAdd)) {
                    throw new \Exception('Một số ghế đã chọn không hợp lệ.');
                }

                $unavailable = $addedSeats->where('status', '!=', 'available');
                if ($unavailable->isNotEmpty()) {
                    $nums = $unavailable->pluck('seat_number')->join(', ');
                    throw new \Exception("Ghế {$nums} vừa bị đặt bởi người khác. Vui lòng chọn lại.");
                }

                foreach ($addedSeats as $seat) {
                    $seat->update(['status' => 'held']);
                    if ($seat->ticket_type_id) {
                        TicketType::where('id', $seat->ticket_type_id)->decrement('quantity', 1);
                    }
                }
            }

            // 3. Tái cấu trúc lại OrderItems và Tickets
            $allOrderSeats = Seat::with('ticketType')
                ->whereIn('seat_number', $newSeatNumbers)
                ->where('event_id', $lockedOrder->event_id)
                ->get();

            OrderItem::where('order_id', $lockedOrder->id)->delete();

            $seatsGrouped = $allOrderSeats->groupBy('ticket_type_id');
            $newTotalAmount = 0;

            foreach ($seatsGrouped as $ticketTypeId => $seats) {
                $ticketType = TicketType::find($ticketTypeId);
                $qty = $seats->count();
                $price = $ticketType ? (float) $ticketType->price : 0;
                $subtotal = $qty * $price;
                $newTotalAmount += $subtotal;

                $orderItem = $lockedOrder->items()->create([
                    'ticket_type_id' => $ticketTypeId,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $subtotal,
                    'status' => 'active',
                ]);

                foreach ($seats as $seat) {
                    $existingTicket = Ticket::where('order_id', $lockedOrder->id)
                        ->where('seat_id', $seat->id)
                        ->first();

                    if ($existingTicket) {
                        $existingTicket->update(['order_item_id' => $orderItem->id]);
                    } else {
                        Ticket::create([
                            'order_id' => $lockedOrder->id,
                            'order_item_id' => $orderItem->id,
                            'seat_id' => $seat->id,
                            'ticket_code' => $this->creationService->generateTicketCode(),
                        ]);
                    }
                }
            }

            $lockedOrder->update(['total_amount' => $newTotalAmount]);

            return $lockedOrder;
        });
    }
}
