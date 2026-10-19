<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyTicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Events & Public Routes
|--------------------------------------------------------------------------
|
| Tuyến đường công khai: Trang chủ, danh sách và chi tiết sự kiện,
| tìm kiếm trực tiếp và xem vé công khai.
|
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Gợi ý tìm kiếm sự kiện trực tiếp (Live Search Suggestions)
Route::get('/events/search/suggestions', [EventController::class, 'searchSuggestions'])
    ->name('events.search.suggestions');

// Danh sách sự kiện (hỗ trợ lọc theo danh mục)
Route::get('/events/{categorySlug?}', [EventController::class, 'index'])
    ->name('events.index');

// Chi tiết sự kiện
Route::get('/event/{slug}', [EventController::class, 'show'])
    ->name('events.show');

// Xem chi tiết vé công khai qua link chia sẻ
Route::get('/tickets/{ticket_code}', [MyTicketController::class, 'publicShow'])
    ->name('tickets.public.show');
