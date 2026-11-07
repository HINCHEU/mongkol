<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Username (or email) and password, for couples without a Google account: they message you on
 * Telegram and you create the account in admin Users. Google users have no password, so they can't use it.
 */
class PasswordLoginController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $login = Str::lower(trim($data['login']));
        // 5 wrong passwords per account and IP per minute, on top of the route's 20 requests per minute.
        $key = 'password-login:'.$login.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'login' => __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        $field = str_contains($login, '@') ? 'email' : 'username';

        if (! Auth::attempt([$field => $login, 'password' => $data['password']], remember: true)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages(['login' => __('The username or password is incorrect.')]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();

        // A full page visit, not an Inertia redirect: the intended page may be /admin (Filament), which
        // Inertia would otherwise fetch by XHR and show in its error modal, where Livewire can't work.
        return Inertia::location(redirect()->intended(route('dashboard'))->getTargetUrl());
    }
}
