<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tin cậy proxy từ ngrok / reverse proxy để nhận diện đúng giao thức HTTPS
        $middleware->trustProxies(at: '*');

        // Bỏ qua kiểm tra CSRF token cho các Webhook bên ngoài (như IPN từ cổng thanh toán VNPay)
        $middleware->validateCsrfTokens(except: [
            '/vnpay-ipn',
        ]);

        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
