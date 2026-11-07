<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Your admin account with a username and password, next to Google sign-in:
 * `php artisan db:seed --class=AdminSeeder`.
 *
 * It's the first ADMIN_EMAILS address (admin also needs it there), so if you already sign in with
 * that Google account, this adds a password to the same account. ADMIN_USERNAME defaults to "admin";
 * without ADMIN_PASSWORD a random one is made once and printed, and never changed on later runs.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.admin_emails')[0] ?? null;

        if (! $email) {
            $this->command?->error('Set ADMIN_EMAILS in .env first: /admin only opens for those emails.');

            return;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name ??= 'Admin';
        $user->role = 'admin';

        $username = Str::lower(config('app.admin_username') ?: 'admin');
        if ($user->username === null && ! User::where('username', $username)->exists()) {
            $user->username = $username;
        }

        $password = config('app.admin_password');
        $generated = ! $password && $user->password === null;
        if ($password || $generated) {
            $user->password = $password ?: ($password = Str::password(16, symbols: false));
        }

        $user->save();

        $this->command?->info("Admin: {$user->email}, username: ".($user->username ?? '(taken, not set)'));
        if ($generated) {
            $this->command?->warn("Password: {$password}  (shown once; change it in admin Users)");
        }
    }
}
