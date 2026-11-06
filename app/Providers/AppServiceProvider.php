<?php

namespace App\Providers;

use App\Services\Themes\ThemeRegistry;
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
    }
}
