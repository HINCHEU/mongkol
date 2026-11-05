<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(fn () => config(['app.admin_emails' => ['admin@example.com']]));

it('sends guests to the login page', function () {
    $this->get('/app')->assertRedirect('/login');
    $this->get('/admin')->assertRedirect('/login');

    $this->withoutVite()->get('/login')
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Auth/Login', false));
});

it('shows the dashboard to a signed-in customer', function () {
    $user = User::factory()->create(['name' => 'Sokha']);

    $this->actingAs($user)->withoutVite()->get('/app')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Home', false)
            ->where('auth.user.name', 'Sokha'));
});

it('opens /admin for an allowlisted admin', function () {
    $admin = User::factory()->admin()->create(['email' => 'admin@example.com']);

    $this->actingAs($admin)->get('/admin')->assertOk();
    $this->actingAs($admin)->get('/admin/users')->assertOk();
});

it('refuses /admin to customers', function () {
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});

it('refuses /admin to an allowlisted email without the admin role', function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']))
        ->get('/admin')->assertForbidden();
});

it('refuses /admin to an admin role whose email left the allowlist', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertForbidden();
});

it('saves the dashboard language and applies it', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->from('/app')->put('/app/locale', ['locale' => 'km'])->assertRedirect('/app');
    expect($user->fresh()->locale)->toBe('km');

    $this->actingAs($user->fresh())->withoutVite()->get('/app')
        ->assertInertia(fn (AssertableInertia $page) => $page->where('locale', 'km'));
});

it('rejects an unknown language', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put('/app/locale', ['locale' => 'fr'])->assertSessionHasErrors('locale');
    expect($user->fresh()->locale)->toBeNull();
});
