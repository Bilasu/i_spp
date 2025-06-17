<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

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
        // Fix untuk string length issues (especially older MySQL)
        Schema::defaultStringLength(191);

        // Force HTTPS di production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
