<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginLinkController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard\EditorController;
use App\Http\Controllers\Dashboard\InvitationController;
use App\Http\Controllers\Dashboard\LocaleController;
use App\Http\Controllers\Public\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
    });
});
