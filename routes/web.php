<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

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
