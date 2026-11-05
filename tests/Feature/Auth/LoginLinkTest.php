<?php

use App\Models\User;
use Illuminate\Support\Facades\URL;

it('creates the user and prints a link that signs them in', function () {
    config(['app.admin_emails' => ['admin@example.com']]);

    $this->artisan('user:login-link', ['email' => 'Admin@Example.com'])
        ->expectsOutputToContain('/auth/link/')
        ->assertSuccessful();

    $user = User::sole();
    expect($user)->email->toBe('admin@example.com')->role->toBe('admin');

    $url = URL::temporarySignedRoute('auth.link', now()->addMinutes(15), ['user' => $user]);
    $this->get($url)->assertRedirect('/app');
    $this->assertAuthenticatedAs($user);
});

it('keeps a non-allowlisted user as a customer', function () {
    $this->artisan('user:login-link', ['email' => 'guest@example.com'])->assertSuccessful();

    expect(User::sole()->role)->toBe('customer');
});

it('rejects a bad email', function () {
    $this->artisan('user:login-link', ['email' => 'not-an-email'])->assertFailed();

    expect(User::count())->toBe(0);
});

it('refuses tampered and expired links', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $url = URL::temporarySignedRoute('auth.link', now()->addMinutes(15), ['user' => $user]);
    $this->get(str_replace("/link/{$user->id}", "/link/{$other->id}", $url))->assertForbidden();

    $expired = URL::temporarySignedRoute('auth.link', now()->subMinute(), ['user' => $user]);
    $this->get($expired)->assertForbidden();

    $this->assertGuest();
});
