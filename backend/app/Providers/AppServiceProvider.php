<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\SmsServiceInterface;
use App\Services\Sms\MockSmsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Lier le Service SMS
        $this->app->singleton(SmsServiceInterface::class, function () {
            // En dev → Mock
            // En prod → TwilioSmsService (à créer plus tard)
            return new MockSmsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
