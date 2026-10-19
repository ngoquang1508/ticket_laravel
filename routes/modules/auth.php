<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Tuyến đường xác thực: Đăng nhập, đăng ký, đăng xuất,
| và quy trình quên mật khẩu xác thực qua mã OTP email.
|
*/

// Đăng nhập
Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'authenticate'])
    ->name('login.authenticate');

// Đăng ký tài khoản
Route::get('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/register', [AuthController::class, 'store'])
    ->name('register.store');

// Đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Quên mật khẩu & xác minh OTP
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
