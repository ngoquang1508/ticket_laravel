<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CheckInController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SeatMapController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
|
| Tuyến đường trang quản trị hệ thống dành riêng cho tài khoản Admin.
| Tiền tố URL: /admin, Tiền tố tên: admin.
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        // Dashboard tổng quan
        Route::get('/', DashboardController::class)->name('dashboard');

        // Quản lý người dùng, địa điểm, danh mục
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('locations', LocationController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Quản lý sự kiện và cấu hình vé, sơ đồ ghế
        Route::resource('events.ticket-types', TicketTypeController::class)->except(['show']);
        Route::get('events/{event}/seat-map', [SeatMapController::class, 'edit'])->name('events.seat-map.edit');
        Route::put('events/{event}/seat-map', [SeatMapController::class, 'update'])->name('events.seat-map.update');
        Route::resource('events', AdminEventController::class);

        // Quản lý đơn hàng
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Quản lý thông báo Admin (Dropdown)
        Route::get('notifications', fn() => redirect()->route('admin.dashboard'));
        Route::get('notifications/dropdown-data', [NotificationController::class, 'dropdownData'])->name('notifications.dropdown-data');
        Route::get('notifications/{id}/read', [NotificationController::class, 'readAndRedirect'])->name('notifications.read');
        Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Check-in vé sự kiện
        Route::get('check-in', [CheckInController::class, 'index'])->name('checkin.index');
        Route::post('check-in/verify', [CheckInController::class, 'verify'])->name('checkin.verify');
        Route::post('check-in/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');
    });
