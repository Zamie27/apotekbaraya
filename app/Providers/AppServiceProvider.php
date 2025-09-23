<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register OrderNotificationService as singleton
        $this->app->singleton(\App\Services\OrderNotificationService::class, function ($app) {
            return new \App\Services\OrderNotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Order Observer for automatic email notifications
        Order::observe(OrderObserver::class);
    }
}
