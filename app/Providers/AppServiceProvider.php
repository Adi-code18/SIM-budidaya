<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!class_exists(\PragmaRX\Google2FA\Google2FA::class, false)) {
            class_alias(\App\Services\Google2FA::class, \PragmaRX\Google2FA\Google2FA::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
