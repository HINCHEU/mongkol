<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginLinkController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordLoginController;
use App\Http\Controllers\Dashboard\EditorController;
use App\Http\Controllers\Dashboard\GuestController;
use App\Http\Controllers\Dashboard\InvitationController;
use App\Http\Controllers\Dashboard\LocaleController;
use App\Http\Controllers\Dashboard\MediaController;
use App\Http\Controllers\Dashboard\ResponsesController;
use App\Http\Controllers\Dashboard\ShareController;
use App\Http\Controllers\Dashboard\UpgradeController;
use App\Http\Controllers\Public\InvitationController as PublicInvitationController;
use App\Http\Controllers\Public\LegalController;
use App\Http\Controllers\Public\ThemeController;
use App\Models\Payment;
use App\Models\Theme;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome', [
        'themes' => Theme::where('is_active', true)->orderBy('sort')->orderBy('name')->get(),
    ]);
});

Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
Route::get('/themes/{slug}', [ThemeController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('themes.show');
Route::get('/themes/{slug}/calendar.ics', [ThemeController::class, 'calendar'])->where('slug', '[a-z0-9-]+')->name('themes.calendar');

Route::get('/{page}', LegalController::class)->whereIn('page', LegalController::PAGES)->name('legal');

Route::middleware(['guest', 'throttle:20,1'])->group(function () {
    // The Telegram link is for couples without Google: they message you and you make them a password account.
    Route::get('/login', fn () => Inertia::render('Auth/Login', ['telegram' => config('services.telegram.support_username')]))->name('login');
    Route::post('/login', PasswordLoginController::class)->name('login.password');
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
});

Route::get('/auth/link/{user}', LoginLinkController::class)->middleware(['signed', 'throttle:10,1'])->name('auth.link');

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('app')->group(function () {
    Route::get('/', [InvitationController::class, 'index'])->name('dashboard');
    Route::put('/locale', LocaleController::class)->name('locale');

    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');

    Route::middleware('can:update,invitation')->prefix('/invitations/{invitation}')->name('invitations.')->group(function () {
        Route::get('/edit/{step?}', [EditorController::class, 'edit'])->whereIn('step', EditorController::STEPS)->name('edit');
        Route::get('/preview', [EditorController::class, 'preview'])->name('preview');

        foreach (EditorController::STEPS as $step) {
            Route::put("/{$step}", [EditorController::class, $step])->name("update.{$step}");
        }

        Route::post('/media', [MediaController::class, 'store'])->middleware('throttle:60,1')->name('media.store');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->scopeBindings()->name('media.destroy');

        Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');
        Route::post('/guests/preview', [GuestController::class, 'preview'])->middleware('throttle:30,1')->name('guests.preview');
        Route::post('/guests/import', [GuestController::class, 'import'])->middleware('throttle:30,1')->name('guests.import');
        Route::get('/guests/export', [GuestController::class, 'export'])->name('guests.export');
        Route::scopeBindings()->group(function () {
            Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
            Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
            Route::put('/guests/{guest}/sent', [GuestController::class, 'sent'])->name('guests.sent');
        });

        Route::get('/share', [ShareController::class, 'show'])->name('share');
        Route::put('/share', [ShareController::class, 'update'])->name('share.update');

        Route::get('/responses', [ResponsesController::class, 'show'])->name('responses');
        Route::put('/responses/settings', [ResponsesController::class, 'settings'])->name('responses.settings');
        Route::get('/responses/export', [ResponsesController::class, 'export'])->name('responses.export');
        Route::scopeBindings()->group(function () {
            Route::put('/wishes/{wish}', [ResponsesController::class, 'moderate'])->name('wishes.moderate');
            Route::delete('/wishes/{wish}', [ResponsesController::class, 'destroyWish'])->name('wishes.destroy');
            Route::delete('/rsvps/{rsvp}', [ResponsesController::class, 'destroyRsvp'])->name('rsvps.destroy');
        });

        Route::get('/upgrade', [UpgradeController::class, 'show'])->name('upgrade');
        Route::post('/upgrade', [UpgradeController::class, 'store'])->name('upgrade.store');
        Route::scopeBindings()->group(function () {
            Route::post('/upgrade/{payment}/proof', [UpgradeController::class, 'proof'])->middleware('throttle:20,1')->name('upgrade.proof');
            Route::delete('/upgrade/{payment}', [UpgradeController::class, 'destroy'])->name('upgrade.destroy');
        });
    });

    // A payment screenshot, for the admin only, through a short-lived signed link from the Payments list.
    Route::get('/admin/payments/{payment}/proof', function (Payment $payment) {
        abort_unless(request()->user()->isAdmin(), 403);
        abort_unless($payment->proof && Storage::disk('local')->exists($payment->proof->path), 404);

        return Storage::disk('local')->response($payment->proof->path);
    })->middleware('signed')->name('admin.payment-proof');
});

// Guest-facing invitation links. Registered last: `/{slug}` would otherwise swallow every route above.
// Reserved words (CreateInvitation::RESERVED_SLUGS) can't become slugs, so they never reach these.
Route::pattern('slug', '[a-z0-9-]+');
Route::pattern('code', '[A-Za-z0-9]{6}');
// shortcut: limits by the IP nginx forwards; behind Cloudflare, trust its CF-Connecting-IP first or every guest shares one IP.
Route::middleware('throttle:guest-pages')->group(function () {
    Route::get('/{slug}/calendar.ics', [PublicInvitationController::class, 'calendar'])->name('invitation.calendar');
    Route::get('/{slug}/{code}/qr.svg', [PublicInvitationController::class, 'qr'])->name('invitation.qr');
    Route::get('/{slug}/{code}', [PublicInvitationController::class, 'show'])->name('invitation.guest');
    Route::get('/{slug}', [PublicInvitationController::class, 'show'])->name('invitation.show');
});
// sendBeacon can't carry the CSRF token; the endpoint only bumps a counter, so it's exempt and throttled.
Route::post('/{slug}/{code}/opened', [PublicInvitationController::class, 'opened'])
    ->withoutMiddleware(ValidateCsrfToken::class)->middleware('throttle:guest-beacon')->name('invitation.opened');
// RSVP and guest book. Guests have no session to protect, so no CSRF token: the limit and a honeypot stop spam.
Route::withoutMiddleware(ValidateCsrfToken::class)->middleware('throttle:guest-forms')->group(function () {
    Route::post('/{slug}/rsvp', [PublicInvitationController::class, 'rsvp'])->name('invitation.rsvp');
    Route::post('/{slug}/wishes', [PublicInvitationController::class, 'wish'])->name('invitation.wish');
    Route::post('/{slug}/{code}/rsvp', [PublicInvitationController::class, 'rsvp'])->name('invitation.guest.rsvp');
    Route::post('/{slug}/{code}/wishes', [PublicInvitationController::class, 'wish'])->name('invitation.guest.wish');
});
