<?php

use App\Jobs\SendTelegramAlert;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\TrialEndingSoon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
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

// Hourly: one "preview ends soon" email per invitation, within its last 12 hours of trial.
Artisan::command('invitations:remind-trial', function () {
    $sent = 0;

    Invitation::trial()->whereNull('trial_reminded_at')->where('trial_ends_at', '<=', now()->addHours(12))
        ->with('user')->each(function (Invitation $invitation) use (&$sent) {
            $invitation->user->notify(new TrialEndingSoon($invitation));
            // saveQuietly: the invitation page doesn't show this, so its cached view stays.
            $invitation->forceFill(['trial_reminded_at' => now()])->saveQuietly();
            $sent++;
        });

    $this->info("Sent {$sent} reminder(s).");
})->purpose('Email couples whose free preview ends within 12 hours');

Schedule::command('invitations:remind-trial')->hourly()->withoutOverlapping();

// For shell scripts (e.g. a failed nightly backup in the Makefile) to reach your Telegram.
Artisan::command('telegram:alert {message}', function () {
    SendTelegramAlert::dispatchSync($this->argument('message'));
})->purpose('Send a message to the admin Telegram chat');
