<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.event-category-nav', function ($view) {
            $view->with('categories', Category::where('is_active', true)->get());
        });

        // Chia sẻ dữ liệu thông báo cho giao diện Admin
        View::composer(['layouts.admin', 'layouts.partials.*', 'admin.*'], function ($view) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                $user = auth()->user();
                $unreadCount = $user->unreadNotifications()->count();
                $totalCount = $user->notifications()->count();
                $latestNotifications = $user->notifications()->latest()->take(25)->get();

                $view->with([
                    'adminUnreadNotificationsCount' => $unreadCount,
                    'adminTotalNotificationsCount' => $totalCount,
                    'adminLatestNotifications' => $latestNotifications,
                ]);
            } else {
                $view->with([
                    'adminUnreadNotificationsCount' => 0,
                    'adminTotalNotificationsCount' => 0,
                    'adminLatestNotifications' => collect(),
                ]);
            }
        });
    }
}
