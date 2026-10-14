<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\SeatMapController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CheckInController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/event/{slug}', [\App\Http\Controllers\EventController::class, 'show'])
    ->name('events.show');

Route::middleware('auth')->group(function () {
    Route::get('/event/{slug}/book', [\App\Http\Controllers\BookingController::class, 'show'])
        ->name('events.book');

    Route::post('/event/{slug}/book', [\App\Http\Controllers\BookingController::class, 'store'])
        ->name('events.book.store');

    Route::post('/orders/{order}/process-payment', [\App\Http\Controllers\BookingController::class, 'processPayment'])
        ->name('orders.process-payment');

    Route::get('/vnpay-return', [\App\Http\Controllers\BookingController::class, 'vnpayReturn'])
        ->name('orders.vnpay.return');

    Route::get('/orders/{order}/success', [\App\Http\Controllers\BookingController::class, 'success'])
        ->name('orders.success');

    Route::get('/orders/{order}/instruction', [\App\Http\Controllers\BookingController::class, 'instruction'])
        ->name('orders.instruction');

    Route::post('/orders/{order}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])
        ->name('orders.cancel');

    Route::post('/orders/{order}/update-tickets', [\App\Http\Controllers\BookingController::class, 'updateTickets'])
        ->name('orders.update-tickets');

    Route::get('/orders/{order}/edit-tickets', [\App\Http\Controllers\BookingController::class, 'editTickets'])
        ->name('orders.edit-tickets');

    Route::post('/orders/{order}/edit-tickets', [\App\Http\Controllers\BookingController::class, 'updateAssignedSeats'])
        ->name('orders.update-assigned-seats');

    Route::get('/my-tickets', [\App\Http\Controllers\MyTicketController::class, 'index'])
        ->name('user.tickets');

    Route::get('/my-tickets/{ticket_code}', [\App\Http\Controllers\MyTicketController::class, 'show'])
        ->name('user.tickets.show');
});

Route::get('/tickets/{ticket_code}', [\App\Http\Controllers\MyTicketController::class, 'publicShow'])
    ->name('tickets.public.show');

Route::get('/events/{categorySlug?}', [\App\Http\Controllers\EventController::class, 'index'])
    ->name('events.index');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

// Login
Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'authenticate'])
    ->name('login.authenticate');

// Register
Route::get('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/register', [AuthController::class, 'store'])
    ->name('register.store');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Password reset by email OTP
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendOtp'])
    ->name('password.send-otp');

Route::get('/forgot-password/verify', [AuthController::class, 'verifyOtp'])
    ->name('password.verify');

Route::post('/forgot-password/verify', [AuthController::class, 'verifyOtpCode'])
    ->name('password.verify-otp');

Route::get('/forgot-password/reset', [AuthController::class, 'resetPassword'])
    ->name('password.reset');

Route::post('/forgot-password/reset', [AuthController::class, 'updatePassword'])
    ->name('password.update');


// Admin routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('users', UserController::class)
            ->except(['show']);
        Route::resource('locations', LocationController::class)
            ->except(['show']);
        Route::resource('categories', CategoryController::class)
            ->except(['show']);
        Route::resource('events.ticket-types', TicketTypeController::class)
            ->except(['show']);
        Route::get('events/{event}/seat-map', [SeatMapController::class, 'edit'])
            ->name('events.seat-map.edit');
        Route::put('events/{event}/seat-map', [SeatMapController::class, 'update'])
            ->name('events.seat-map.update');

        Route::resource('events', EventController::class);

        // Quản lý đơn hàng
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Quản lý thông báo (dropdown)
        Route::get('notifications', fn() => redirect()->route('admin.dashboard'));
        Route::get('notifications/dropdown-data', [NotificationController::class, 'dropdownData'])->name('notifications.dropdown-data');
        Route::get('notifications/{id}/read', [NotificationController::class, 'readAndRedirect'])->name('notifications.read');
        Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Check-in vé bằng QR
        Route::get('check-in', [CheckInController::class, 'index'])->name('checkin.index');
        Route::post('check-in/verify', [CheckInController::class, 'verify'])->name('checkin.verify');
        Route::post('check-in/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');
    });
