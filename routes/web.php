<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginLinkController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard\EditorController;
use App\Http\Controllers\Dashboard\GuestController;
use App\Http\Controllers\Dashboard\InvitationController;
use App\Http\Controllers\Dashboard\LocaleController;
use App\Http\Controllers\Dashboard\MediaController;
use App\Http\Controllers\Dashboard\ShareController;
use App\Http\Controllers\Public\InvitationController as PublicInvitationController;
use App\Http\Controllers\Public\ThemeController;
use App\Models\Theme;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'themes' => Theme::where('is_active', true)->orderBy('sort')->orderBy('name')->get(),
    ]);
});

Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
Route::get('/themes/{slug}', [ThemeController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('themes.show');
Route::get('/themes/{slug}/calendar.ics', [ThemeController::class, 'calendar'])->where('slug', '[a-z0-9-]+')->name('themes.calendar');

Route::middleware('guest')->group(function () {
    Route::inertia('/login', 'Auth/Login')->name('login');
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
        Route::post('/guests/preview', [GuestController::class, 'preview'])->name('guests.preview');
        Route::post('/guests/import', [GuestController::class, 'import'])->name('guests.import');
        Route::get('/guests/export', [GuestController::class, 'export'])->name('guests.export');
        Route::scopeBindings()->group(function () {
            Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
            Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
            Route::put('/guests/{guest}/sent', [GuestController::class, 'sent'])->name('guests.sent');
        });

        Route::get('/share', [ShareController::class, 'show'])->name('share');
        Route::put('/share', [ShareController::class, 'update'])->name('share.update');
    });
});

// Guest-facing invitation links. Registered last: `/{slug}` would otherwise swallow every route above.
// Reserved words (CreateInvitation::RESERVED_SLUGS) can't become slugs, so they never reach these.
Route::pattern('slug', '[a-z0-9-]+');
Route::pattern('code', '[A-Za-z0-9]{6}');
Route::get('/{slug}/calendar.ics', [PublicInvitationController::class, 'calendar'])->name('invitation.calendar');
Route::get('/{slug}/{code}/qr.svg', [PublicInvitationController::class, 'qr'])->name('invitation.qr');
// sendBeacon can't carry the CSRF token; the endpoint only bumps a counter, so it's exempt and throttled.
Route::post('/{slug}/{code}/opened', [PublicInvitationController::class, 'opened'])
    ->withoutMiddleware(ValidateCsrfToken::class)->middleware('throttle:30,1')->name('invitation.opened');
Route::get('/{slug}/{code}', [PublicInvitationController::class, 'show'])->name('invitation.guest');
Route::get('/{slug}', [PublicInvitationController::class, 'show'])->name('invitation.show');
