<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;
use Livewire\Livewire;

beforeEach(fn () => $this->withoutVite());

it('signs in with a username or email and password, and refuses a wrong one', function () {
    $user = User::factory()->create(['username' => 'sokha', 'email' => 'sokha@example.com', 'google_id' => null, 'password' => 'correct horse']);

    $this->post('/login', ['login' => 'Sokha', 'password' => 'wrong'])->assertSessionHasErrors('login');
    $this->assertGuest();

    $this->post('/login', ['login' => 'Sokha ', 'password' => 'correct horse'])->assertRedirect('/app');
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->last_login_at)->not->toBeNull();

    auth()->logout();
    $this->post('/login', ['login' => 'sokha@example.com', 'password' => 'correct horse'])->assertRedirect('/app');
});

it('locks an account out for a minute after 5 wrong passwords', function () {
    $this->freezeTime();
    User::factory()->create(['username' => 'dara', 'google_id' => null, 'password' => 'correct horse']);

    foreach (range(1, 5) as $_) {
        $this->post('/login', ['login' => 'dara', 'password' => 'nope']);
    }

    $this->post('/login', ['login' => 'dara', 'password' => 'correct horse'])
        ->assertSessionHasErrors(['login' => __('Too many attempts. Please try again in :seconds seconds.', ['seconds' => 60])]);
    $this->assertGuest();
});

it('keeps Google accounts, which have no password, out of the password form', function () {
    $google = User::factory()->create(['email' => 'g@example.com']);
    expect($google->password)->toBeNull();

    $this->post('/login', ['login' => 'g@example.com', 'password' => ''])->assertSessionHasErrors('password');
    $this->assertGuest();
});

it('shows the Telegram contact on the login page when it is set', function () {
    $this->get('/login')->assertInertia(fn (AssertableInertia $page) => $page->component('Auth/Login', false)->where('telegram', null));

    config(['services.telegram.support_username' => 'mongkol_bot']);
    $this->get('/login')->assertInertia(fn (AssertableInertia $page) => $page->where('telegram', 'mongkol_bot'));
});

it('lets the admin make a password account without an email', function () {
    config(['app.admin_emails' => ['admin@example.com']]);
    $this->actingAs(User::factory()->admin()->create(['email' => 'admin@example.com']));

    Livewire::test(CreateUser::class)
        ->fillForm(['name' => 'Chan Tha', 'username' => 'ChanTha', 'password' => 'secret-pass', 'role' => 'customer'])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::firstWhere('username', 'chantha');
    expect($user)->email->toBeNull()->google_id->toBeNull()->isAdmin()->toBeFalse()
        ->and(Hash::check('secret-pass', $user->password))->toBeTrue();

    // A new password on edit; leaving it empty keeps it. The log says it changed, never the hash.
    Livewire::test(EditUser::class, ['record' => $user->id])->fillForm(['password' => 'another-pass'])->call('save')->assertHasNoFormErrors();
    Livewire::test(EditUser::class, ['record' => $user->id])->fillForm(['name' => 'Chan Tha Sr'])->call('save');
    Livewire::test(EditUser::class, ['record' => $user->id])->fillForm(['role' => 'admin'])->call('save');
    expect($user->fresh()->role)->toBe('admin'); // saved now (the form used to drop it), though without an allowlisted email it isn't admin
    expect(Hash::check('another-pass', $user->fresh()->password))->toBeTrue()
        ->and(ActivityLog::where('action', 'user.updated')->first()->changes)->toBe(['password' => '(changed)']);

    Livewire::test(CreateUser::class)->fillForm(['name' => 'No login', 'password' => 'secret-pass', 'role' => 'customer'])
        ->call('create')->assertHasFormErrors(['username']); // needs a username or an email

    auth()->logout();
    $this->post('/login', ['login' => 'chantha', 'password' => 'another-pass'])->assertRedirect('/app');
});

it('seeds your admin account with a password, on your existing Google account', function () {
    config(['app.admin_emails' => ['boss@example.com'], 'app.admin_username' => 'boss', 'app.admin_password' => null]);
    $google = User::factory()->create(['email' => 'boss@example.com']);

    $this->seed(AdminSeeder::class);
    $admin = $google->fresh();
    expect($admin)->role->toBe('admin')->username->toBe('boss')->google_id->toBe($google->google_id)
        ->and($admin->password)->not->toBeNull()
        ->and($admin->isAdmin())->toBeTrue();

    $this->seed(AdminSeeder::class); // a second run keeps the printed password
    expect($admin->fresh()->password)->toBe($admin->password)->and(User::count())->toBe(1);

    config(['app.admin_password' => 'chosen-password']);
    $this->seed(AdminSeeder::class);
    $this->post('/login', ['login' => 'boss', 'password' => 'chosen-password'])->assertRedirect('/app');
});
