<?php

namespace App\Constants;

/**
 * BookingConstant
 *
 * Định nghĩa các hằng số cấu hình nghiệp vụ đặt vé và đơn hàng trong toàn bộ ứng dụng.
 */
class BookingConstant
{
    /**
     * Số lượng vé tối đa được phép chọn trong một lần đặt (mỗi đơn hàng)
     */
    public const MAX_TICKETS_PER_ORDER = 10;

    /**
     * Số lượng vé tối đa một tài khoản người dùng được phép mua cho một sự kiện
     */
    public const MAX_TICKETS_PER_USER = 10;

    /**
     * Thời gian giữ vé tạm thời (tính bằng phút) trước khi đơn hàng hết hạn thanh toán
     */
    public const ORDER_EXPIRE_MINUTES = 10;
}
