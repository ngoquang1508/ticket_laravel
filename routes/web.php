<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Modular Route Organization
|--------------------------------------------------------------------------
|
| Hệ thống định tuyến được module hóa thành các tệp riêng biệt theo từng miền nghiệp vụ:
| 1. Sự kiện công khai & Trang chủ (Events & Public)
| 2. Xác thực tài khoản (Authentication & OTP)
| 3. Đặt vé & Đơn hàng (Booking & Orders)
| 4. Cá nhân người dùng (User Profile & My Tickets)
| 5. Cổng quản trị viên (Admin Portal)
|
*/

// Trang chủ và xem sự kiện công khai
require __DIR__ . '/modules/events.php';

// Xác thực tài khoản, đăng nhập, đăng ký và quên mật khẩu
require __DIR__ . '/modules/auth.php';

// Quy trình đặt vé, chọn ghế, thanh toán và hủy đơn
require __DIR__ . '/modules/booking.php';

// Quản lý vé cá nhân và hồ sơ người dùng
require __DIR__ . '/modules/user.php';

// Khu vực quản trị hệ thống dành riêng cho Admin
require __DIR__ . '/modules/admin.php';

// Bắt tất cả các đường dẫn không tồn tại và trả về view lỗi 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
