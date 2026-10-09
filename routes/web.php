<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\SeatMapController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/event/{slug}', [\App\Http\Controllers\EventController::class, 'show'])
    ->name('events.show');

Route::get('/event/{slug}/book', [\App\Http\Controllers\BookingController::class, 'show'])
    ->name('events.book');

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
    });
