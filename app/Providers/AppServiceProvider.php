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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS on Render (behind reverse proxy)
        // Render uses X-Forwarded-Proto header to indicate HTTPS
        if (request()->header('X-Forwarded-Proto') === 'https' || 
            request()->header('X-Forwarded-Ssl') === 'on' ||
            (env('APP_ENV') === 'production' && str_contains(env('APP_URL', ''), 'https'))) {
            \URL::forceScheme('https');
        }
    }
}
