<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Notifications\AdminOrderNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
     * Lấy đơn hàng pending của user cho sự kiện cụ thể nếu còn trong thời gian giữ vé.
     *
     * @param  Event  $event
     * @param  int  $userId
     * @return Order|null
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
     * Tạo order mới. Nếu đã có order pending còn hạn thì trả về order hiện tại.
     *
     * @param  Event  $event
     * @param  array  $input  Dữ liệu từ request (đã qua validate cơ bản)
     * @return Order
     *
     * @throws \Exception
     */
    public function createOrder(Event $event, array $input): Order
    {
        $userId = auth()->id();
        if ($userId) {
            // Tự động dọn dẹp các đơn hết hạn của user
            $this->cancelExpiredOrders($userId);

            // Kiểm tra xem user đã có order pending còn trong thời gian giữ vé hay chưa
            $existingOrder = $this->getActivePendingOrder($event, $userId);
            if ($existingOrder) {
                return $existingOrder;
            }
        }

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

        // Bắn thông báo đơn mới thanh toán cho toàn bộ Admin
        $this->notifyAdmins(AdminOrderNotification::paid($order));
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

        // Bắn thông báo đơn hàng chờ duyệt cho Admin
        $this->notifyAdmins(AdminOrderNotification::awaitingConfirmation($order));
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

            if (!in_array($locked->status, ['processing', 'pending'])) {
                throw new \Exception('Đơn hàng không ở trạng thái chờ xử lý hoặc chờ thanh toán.');
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

        // Gửi email thông báo (xử lý an toàn nếu chưa cấu hình mail server)
        try {
            $recipientEmail = $order->user?->email ?: $order->customer_email;
            if ($recipientEmail) {
                \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\OrderConfirmed($order));
            }
        } catch (\Throwable $e) {
            Log::warning('Không thể gửi email xác nhận đơn hàng: ' . $e->getMessage());
        }

        // Bắn thông báo đơn mới thanh toán cho Admin
        $this->notifyAdmins(AdminOrderNotification::paid($order));
    }

    /**
     * Gửi thông báo đến toàn bộ tài khoản Admin trong hệ thống
     */
    protected function notifyAdmins($notification): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, $notification);
            }
        } catch (\Throwable $e) {
            Log::warning('Lỗi gửi thông báo cho Admin: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PHẦN 3: HỦY / HẾT HẠN ORDER
    // =========================================================================

    /**
     * Hủy order (hết hạn hoặc người dùng/admin hủy).
     * Chống hủy nhiều lần bằng kiểm tra trạng thái.
     * Hủy đơn hàng và giải phóng ghế / hoàn lại số lượng vé.
     *
     * @param  Order        $order
     * @param  string       $newStatus      'cancelled' hoặc 'expired'
     * @param  string|null  $cancelReason   Lý do hủy đơn hàng
     * @return void
     */
    public function cancelOrder(Order $order, string $newStatus = 'cancelled', ?string $cancelReason = null): void
    {
        DB::transaction(function () use ($order, $newStatus, $cancelReason) {
            // Khóa dòng order để tránh cancel nhiều lần
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();

            // Chỉ cho phép cancel khi order đang pending hoặc processing
            if (!in_array($locked->status, ['pending', 'processing'])) {
                // Nếu đã expired/cancelled/completed → không làm gì
                return;
            }

            $isAssignedSeat = $locked->event->sale_mode === 'assigned_seat';

            // Cập nhật trạng thái order và lý do hủy
            $updateData = ['status' => $newStatus];
            if ($cancelReason) {
                $updateData['cancel_reason'] = $cancelReason;
            }
            $locked->update($updateData);

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
                TicketType::where('id', $item->ticket_type_id)
                    ->increment('quantity', $item->quantity);
            }

            // KHÔNG xóa Order, OrderItem, Ticket để giữ lịch sử
        });
    }

    /**
     * Tự động quét và hủy các đơn hàng pending đã quá hạn thời gian giữ vé.
     * Hoàn trả vé/ghế, chuyển trạng thái sang cancelled và ghi nhận lý do 'Hết hạn thanh toán'.
     *
     * @param int|null $userId Nếu truyền userId thì chỉ quét cho user đó, nếu null thì quét toàn hệ thống.
     * @return int Số lượng đơn hàng đã hủy.
     */
    public function cancelExpiredOrders(?int $userId = null): int
    {
        $query = Order::where('status', 'pending')
            ->where('expires_at', '<=', now());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $expiredOrders = $query->get();
        $count = 0;

        foreach ($expiredOrders as $order) {
            $this->cancelOrder($order, 'cancelled', 'Hết hạn thanh toán');
            $count++;
        }

        return $count;
    }

    /**
     * Chỉnh sửa số lượng vé cho đơn hàng ở trạng thái Pending.
     * Người dùng có thể thay đổi số lượng từng loại vé trong phạm vi cho phép (tồn kho, quota, max order).
     *
     * @param  Order  $order
     * @param  array  $quantities  Mảng [ticket_type_id => new_quantity]
     * @return Order
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
                $this->cancelOrder($lockedOrder, 'cancelled', 'Hết hạn thanh toán');
                throw new \Exception('Đơn hàng đã hết thời gian giữ vé và đã bị hủy tự động.');
            }

            if ($lockedOrder->event->sale_mode === 'assigned_seat') {
                throw new \Exception('Sự kiện chọn ghế theo sơ đồ không hỗ trợ sửa số lượng vé trực tiếp.');
            }

            // Lọc và tính tổng số vé yêu cầu mới
            $sanitizedQuantities = [];
            foreach ($quantities as $ttId => $qty) {
                $sanitizedQuantities[(int)$ttId] = max(0, (int)$qty);
            }

            $totalRequested = array_sum($sanitizedQuantities);

            if ($totalRequested <= 0) {
                throw new \Exception('Đơn hàng phải có ít nhất 1 vé. Nếu muốn hủy toàn bộ, vui lòng bấm "Hủy đơn hàng".');
            }

            if ($totalRequested > self::MAX_TICKETS_PER_ORDER) {
                throw new \Exception('Bạn chỉ được chọn tối đa ' . self::MAX_TICKETS_PER_ORDER . ' vé trong một đơn hàng.');
            }

            // Kiểm tra quota tài khoản: tính chênh lệch tăng thêm
            $currentTotal = $lockedOrder->items()->where('status', 'active')->sum('quantity');
            $increase = $totalRequested - $currentTotal;
            if ($increase > 0) {
                $this->assertUserQuota($lockedOrder->event, $lockedOrder->user_id, $increase);
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
                    // Tăng vé -> kiểm tra số lượng tồn kho còn lại
                    if ($ticketType->quantity < $diff) {
                        throw new \Exception("Loại vé \"{$ticketType->name}\" không đủ số lượng (chỉ còn thêm {$ticketType->quantity} vé).");
                    }

                    // Giữ thêm vé
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

                    // Tạo thêm diff bản ghi Ticket
                    for ($i = 0; $i < $diff; $i++) {
                        Ticket::create([
                            'order_id' => $lockedOrder->id,
                            'order_item_id' => $orderItem->id,
                            'seat_id' => null,
                            'ticket_code' => $this->generateTicketCode(),
                        ]);
                    }
                } elseif ($diff < 0) {
                    // Giảm vé -> hoàn trả lại kho
                    $returnCount = abs($diff);
                    $ticketType->increment('quantity', $returnCount);

                    if ($newQty === 0) {
                        // Xóa tickets và orderItem
                        Ticket::where('order_id', $lockedOrder->id)
                            ->where('order_item_id', $orderItem->id)
                            ->delete();
                        $orderItem->delete();
                    } else {
                        $orderItem->update([
                            'quantity' => $newQty,
                            'total' => $newQty * $orderItem->price,
                        ]);

                        // Xóa bớt returnCount vé của item này
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

            // Cập nhật lại tổng tiền đơn hàng
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
            $this->cancelOrder($order, 'cancelled', 'Hết hạn thanh toán');
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

        if ($count > self::MAX_TICKETS_PER_ORDER) {
            throw new \Exception('Bạn chỉ được chọn tối đa ' . self::MAX_TICKETS_PER_ORDER . ' vé trong một đơn hàng.');
        }

        return DB::transaction(function () use ($order, $newSeatNumbers, $count) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$lockedOrder || $lockedOrder->status !== 'pending') {
                throw new \Exception('Trạng thái đơn hàng không hợp lệ.');
            }

            // Lấy danh sách vé và ghế hiện tại của đơn hàng
            $currentTickets = Ticket::with('seat')
                ->where('order_id', $lockedOrder->id)
                ->get();
            $oldSeatNumbers = $currentTickets->pluck('seat.seat_number')->filter()->values()->toArray();

            // Tính chênh lệch quota nếu số ghế tăng lên
            $increase = $count - count($oldSeatNumbers);
            if ($increase > 0) {
                $this->assertUserQuota($lockedOrder->event, $lockedOrder->user_id, $increase);
            }

            $seatsToRemove = array_diff($oldSeatNumbers, $newSeatNumbers);
            $seatsToAdd = array_diff($newSeatNumbers, $oldSeatNumbers);

            // 1. Giải phóng các ghế bị bỏ chọn ($seatsToRemove)
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

            // 2. Giữ các ghế mới thêm vào ($seatsToAdd)
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

            // Xóa toàn bộ OrderItems cũ
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
                            'ticket_code' => $this->generateTicketCode(),
                        ]);
                    }
                }
            }

            $lockedOrder->update(['total_amount' => $newTotalAmount]);

            return $lockedOrder;
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
            'payment_method' => null, // chưa chọn phương thức, cập nhật khi người dùng xác nhận ở checkout
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
