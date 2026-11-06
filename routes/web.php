<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginLinkController;
use App\Http\Controllers\Auth\LogoutController;
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
    Route::inertia('/', 'Home')->name('dashboard');
    Route::put('/locale', LocaleController::class)->name('locale');
});
