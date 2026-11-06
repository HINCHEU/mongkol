<?php

namespace App\Providers;

use App\Services\Themes\ThemeRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeRegistry::class, fn () => new ThemeRegistry([resource_path('themes')]));
    }

    public function boot(): void
    {
        $this->app->make(ThemeRegistry::class)->registerViews();
    }
}
