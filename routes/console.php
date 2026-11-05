<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

// shortcut: the link works for anyone holding it until it expires, not once; fine while only the admin runs this.
Artisan::command('user:login-link {email} {--name=} {--minutes=15}', function () {
    $email = Str::lower($this->argument('email'));

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error("'{$email}' is not an email address.");

        return 1;
    }

    $user = User::firstOrCreate(['email' => $email], ['name' => $this->option('name') ?: Str::before($email, '@')]);

    // Same rule as Google login: ADMIN_EMAILS decides who becomes admin.
    if ($user->isAllowlistedAdmin() && $user->role !== 'admin') {
        $user->forceFill(['role' => 'admin'])->save();
    }

    $minutes = (int) $this->option('minutes');
    $this->line(URL::temporarySignedRoute('auth.link', now()->addMinutes($minutes), ['user' => $user]));
    $this->comment("Sign-in link for {$user->email} ({$user->role}), valid {$minutes} minutes.");
})->purpose('Create the user if needed and print a temporary sign-in link (no Google needed)');
