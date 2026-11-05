<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class GoogleController extends Controller
{
    public function redirect(): SymfonyRedirect
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        // The user cancelled on the Google consent screen.
        if ($request->has('error')) {
            return redirect()->route('login');
        }

        try {
            $google = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')->with('error', 'Your sign-in expired. Please try again.');
        }

        // Matching on email is only safe when Google has verified it.
        if (! ($google->user['email_verified'] ?? false)) {
            return redirect()->route('login')->with('error', 'Please verify your Google email first.');
        }

        $email = Str::lower($google->getEmail());

        $user = User::where('google_id', $google->getId())->first()
            ?? User::where('email', $email)->first()
            ?? new User(['email' => $email]);

        $user->fill([
            'google_id' => $google->getId(),
            'name' => $google->getName() ?: $email,
            'avatar_url' => $google->getAvatar(),
            'last_login_at' => now(),
        ]);

        if ($user->isAllowlistedAdmin()) {
            $user->role = 'admin';
        }

        $user->save();

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
