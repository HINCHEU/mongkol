<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;

function fakeGoogle(array $overrides = []): void
{
    $data = array_merge([
        'id' => 'google-123',
        'name' => 'Sokha Chan',
        'email' => 'Sokha@Example.com',
        'avatar' => 'https://lh3.googleusercontent.com/a/sokha',
        'email_verified' => true,
    ], $overrides);

    $user = (new GoogleUser)
        ->setRaw(['email_verified' => $data['email_verified']])
        ->map(collect($data)->only('id', 'name', 'email', 'avatar')->all());

    Socialite::shouldReceive('driver->user')->andReturn($user);
}

it('redirects to Google', function () {
    config(['services.google.client_id' => 'test-client']);

    $this->get('/auth/google')->assertRedirectContains('accounts.google.com');
});

it('creates a customer on first login', function () {
    fakeGoogle();

    $this->get('/auth/google/callback')->assertRedirect('/app');

    $user = User::sole();
    expect($user)
        ->email->toBe('sokha@example.com')
        ->google_id->toBe('google-123')
        ->name->toBe('Sokha Chan')
        ->avatar_url->toBe('https://lh3.googleusercontent.com/a/sokha')
        ->role->toBe('customer')
        ->last_login_at->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});

it('links an existing account by email', function () {
    $existing = User::factory()->create(['email' => 'sokha@example.com', 'google_id' => null]);
    fakeGoogle();

    $this->get('/auth/google/callback');

    expect(User::count())->toBe(1)
        ->and($existing->fresh()->google_id)->toBe('google-123');
    $this->assertAuthenticatedAs($existing);
});

it('finds the user by google id even after their email changed', function () {
    $existing = User::factory()->create(['email' => 'old@example.com', 'google_id' => 'google-123']);
    fakeGoogle(['email' => 'new@example.com']);

    $this->get('/auth/google/callback');

    expect(User::count())->toBe(1);
    $this->assertAuthenticatedAs($existing);
});

it('refuses an unverified Google email', function () {
    User::factory()->create(['email' => 'sokha@example.com', 'google_id' => null]);
    fakeGoogle(['email_verified' => false]);

    $this->get('/auth/google/callback')
        ->assertRedirect('/login')
        ->assertSessionHas('error');

    $this->assertGuest();
});

it('sends a cancelled or expired sign-in back to login', function () {
    $this->get('/auth/google/callback?error=access_denied')->assertRedirect('/login');

    Socialite::shouldReceive('driver->user')->andThrow(new InvalidStateException);
    $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHas('error');

    $this->assertGuest();
});

it('promotes an allowlisted email to admin', function () {
    config(['app.admin_emails' => ['sokha@example.com']]);
    fakeGoogle();

    $this->get('/auth/google/callback');

    expect(User::sole()->role)->toBe('admin');
});

it('logs out', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});
