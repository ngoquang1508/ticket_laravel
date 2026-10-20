<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Booking & Order Routes
|--------------------------------------------------------------------------
|
| Tuyến đường đặt vé, giữ chỗ, xử lý thanh toán (VNPay / Trực tiếp),
| hủy đơn hàng và chỉnh sửa số lượng vé / ghế ngồi.
|
*/

Route::middleware('auth')->group(function () {
    // Đặt vé sự kiện
    Route::get('/event/{slug}/book', [BookingController::class, 'show'])
        ->name('events.book');

    Route::post('/event/{slug}/book', [BookingController::class, 'store'])
        ->name('events.book.store');

    // Xử lý thanh toán
    Route::post('/orders/{order}/process-payment', [BookingController::class, 'processPayment'])
        ->name('orders.process-payment');

    // Màn hình kết quả & hướng dẫn thanh toán
    Route::get('/orders/{order}/success', [BookingController::class, 'success'])
        ->name('orders.success');

    Route::get('/orders/{order}/instruction', [BookingController::class, 'instruction'])
        ->name('orders.instruction');

    // Thao tác hủy & chỉnh sửa đơn hàng
    Route::post('/orders/{order}/cancel', [BookingController::class, 'cancel'])
        ->name('orders.cancel');

    Route::post('/orders/{order}/update-tickets', [BookingController::class, 'updateTickets'])
        ->name('orders.update-tickets');

    Route::get('/orders/{order}/edit-tickets', [BookingController::class, 'editTickets'])
        ->name('orders.edit-tickets');

    Route::post('/orders/{order}/edit-tickets', [BookingController::class, 'updateAssignedSeats'])
        ->name('orders.update-assigned-seats');
});

// Tuyến đường nhận kết quả thanh toán từ VNPay (công khai, được bảo vệ bằng chữ ký số HMAC-SHA512)
Route::get('/vnpay-return', [BookingController::class, 'vnpayReturn'])
    ->name('orders.vnpay.return');

// Tuyến đường Webhook IPN (Server-to-Server) nhận thông báo thanh toán tự động từ VNPay
Route::match(['get', 'post'], '/vnpay-ipn', [BookingController::class, 'vnpayIpn'])
    ->name('orders.vnpay.ipn');
