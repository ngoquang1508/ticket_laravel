<?php

use App\Http\Controllers\MyTicketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Portal Routes
|--------------------------------------------------------------------------
|
| Tuyến đường dành cho người dùng đã đăng nhập:
| Quản lý vé của tôi (My Tickets) và thông tin cá nhân (Profile).
|
*/

Route::middleware('auth')->group(function () {
    // Quản lý vé của tôi
    Route::get('/my-tickets', [MyTicketController::class, 'index'])
        ->name('user.tickets');

    Route::get('/my-tickets/{ticket_code}', [MyTicketController::class, 'show'])
        ->name('user.tickets.show');

    // Quản lý thông tin tài khoản & mật khẩu
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('user.profile');

    Route::put('/profile', [ProfileController::class, 'updateProfile'])
        ->name('user.profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('user.profile.password');
});
