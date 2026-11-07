<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Jobs\SendTelegramAlert;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Nginx Proxy Manager terminates HTTPS; trust its X-Forwarded-* from the Docker networks only.
        $middleware->trustProxies(at: ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16']);

        // Global, so the Filament admin (its own middleware stack) gets the headers too.
        $middleware->append(SecurityHeaders::class);

        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Real errors only (404s, validation and auth aren't reported), once per error per 30 minutes.
        $exceptions->reportable(function (Throwable $e) {
            try {
                if (! app()->isProduction() || ! Cache::add('error-alert:'.md5($e::class.$e->getMessage()), true, now()->addMinutes(30))) {
                    return;
                }

                SendTelegramAlert::dispatchSync(Str::limit(implode("\n", [
                    '⚠️ Mongkol error',
                    class_basename($e).': '.$e->getMessage(),
                    str_replace(base_path().'/', '', $e->getFile()).':'.$e->getLine(),
                    app()->runningInConsole() ? 'console' : request()->method().' '.request()->fullUrl(),
                ]), 1000));
            } catch (Throwable) {
                // Best effort: the database or cache may be what failed. The log still has the error.
            }
        });
    })->create();
