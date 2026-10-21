<?php

namespace App\Services;

use App\Constants\BookingConstant;
use App\Models\Event;
use App\Models\Order;
use App\Services\Booking\OrderAmendmentService;
use App\Services\Booking\OrderCancellationService;
use App\Services\Booking\OrderCreationService;
use App\Services\Booking\OrderPaymentService;
use Carbon\Carbon;

/**
 * BookingService
 *
 * Lớp điều phối trung tâm (Facade / Orchestrator) cho toàn bộ quy trình đặt vé,
 * thanh toán, hủy đơn và chỉnh sửa vé.
 * 
 * Các logic chi tiết được phân rã theo nguyên lý Single Responsibility Principle (SRP)
 * vào các service chuyên biệt trong namespace App\Services\Booking:
 * - OrderCreationService: Tạo đơn hàng, kiểm tra quota và tồn kho vé/ghế.
 * - OrderPaymentService: Xác nhận thanh toán, direct payment và thông báo admin.
 * - OrderCancellationService: Hủy đơn, giải phóng ghế/vé và dọn dẹp đơn hết hạn.
 * - OrderAmendmentService: Chỉnh sửa số lượng vé hoặc đổi ghế đã chọn.
 */
class BookingService
{
    public const MAX_TICKETS_PER_ORDER = BookingConstant::MAX_TICKETS_PER_ORDER;
    public const MAX_TICKETS_PER_USER = BookingConstant::MAX_TICKETS_PER_USER;
    public const ORDER_EXPIRE_MINUTES = BookingConstant::ORDER_EXPIRE_MINUTES;

    protected OrderCreationService $creationService;
    protected OrderPaymentService $paymentService;
    protected OrderCancellationService $cancellationService;
    protected OrderAmendmentService $amendmentService;

    public function __construct(
        OrderCreationService $creationService,
        OrderPaymentService $paymentService,
        OrderCancellationService $cancellationService,
        OrderAmendmentService $amendmentService
    ) {
        $this->creationService = $creationService;
        $this->paymentService = $paymentService;
        $this->cancellationService = $cancellationService;
        $this->amendmentService = $amendmentService;
    }

    /**
     * Lấy đơn hàng pending của user cho sự kiện cụ thể nếu còn trong thời gian giữ vé.
     */
    public function getActivePendingOrder(Event $event, int $userId): ?Order
    {
        return $this->creationService->getActivePendingOrder($event, $userId);
    }

    /**
     * Lấy số vé tối đa user còn có thể mua cho sự kiện này.
     */
    public function getUserRemainingQuota(Event $event, ?int $userId): int
    {
        return $this->creationService->getUserRemainingQuota($event, $userId);
    }

    /**
     * Tạo order mới. Nếu đã có order pending còn hạn thì trả về order hiện tại.
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

        return $this->creationService->createOrder($event, $input);
    }

    /**
     * Xác nhận đơn sau khi thanh toán thành công.
     *
     * @throws \Exception
     */
    public function confirmOrder(Order $order, string $paymentRef = '', string $paymentMethod = 'direct'): void
    {
        $this->paymentService->confirmOrder($order, $paymentRef, $paymentMethod);
    }

    /**
     * Đánh dấu đơn hàng chờ thanh toán trực tiếp (chờ admin duyệt).
     *
     * @throws \Exception
     */
    public function markAsProcessing(Order $order, ?Carbon $expireAt = null): void
    {
        $this->paymentService->markAsProcessing($order, $expireAt);
    }

    /**
     * Admin duyệt đơn hàng thanh toán trực tiếp.
     *
     * @throws \Exception
     */
    public function adminConfirmOrder(Order $order): void
    {
        $this->paymentService->adminConfirmOrder($order);
    }

    /**
     * Hủy đơn hàng và hoàn trả ghế/vé.
     */
    public function cancelOrder(Order $order, string $newStatus = 'cancelled', ?string $cancelReason = null): void
    {
        $this->cancellationService->cancelOrder($order, $newStatus, $cancelReason);
    }

    /**
     * Tự động quét và hủy các đơn hàng đã quá hạn giữ vé.
     */
    public function cancelExpiredOrders(?int $userId = null): int
    {
        return $this->cancellationService->cancelExpiredOrders($userId);
    }

    /**
     * Chỉnh sửa số lượng vé cho đơn hàng ở trạng thái Pending.
     *
     * @throws \Exception
     */
    public function updateOrderTickets(Order $order, array $quantities): Order
    {
        return $this->amendmentService->updateOrderTickets($order, $quantities);
    }

    /**
     * Cập nhật danh sách ghế ngồi cho đơn hàng pending của sự kiện assigned_seat.
     *
     * @throws \Exception
     */
    public function updateAssignedSeatOrder(Order $order, array $newSeatNumbers): Order
    {
        return $this->amendmentService->updateAssignedSeatOrder($order, $newSeatNumbers);
    }

    /**
     * Kiểm tra sự kiện có thể đặt vé không.
     *
     * @throws \Exception
     */
    public function assertEventBookable(Event $event): void
    {
        $this->creationService->assertEventBookable($event);
    }
}
