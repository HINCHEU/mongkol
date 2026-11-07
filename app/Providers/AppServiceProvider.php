<?php

namespace App\Providers;

use App\Services\Themes\ThemeRegistry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeRegistry::class, fn () => new ThemeRegistry([resource_path('themes')]));
    }

    public function boot(): void
    {
        if (str_starts_with(config('app.url', ''), 'https://') || $this->app->isProduction()) {
            URL::forceScheme('https');
        }

        $this->app->make(ThemeRegistry::class)->registerViews();

        // Named, so each has its own counter (plain throttle:N,1 limits share one per IP).
        // Guest pages: generous for a family on one connection, hopeless for guessing guest codes (57^6).
        RateLimiter::for('guest-pages', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
        RateLimiter::for('guest-beacon', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));
    }
}
