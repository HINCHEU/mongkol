<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const LOCALES = ['km', 'en'];

    protected $fillable = [
        'name',
        'username',
        'email',
        'google_id',
        'avatar_url',
        'locale',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAllowlistedAdmin(): bool
    {
        // Password-only accounts have no email, so they can never be admins.
        return $this->email !== null && in_array(Str::lower($this->email), config('app.admin_emails'), true);
    }

    // Both checks, so removing an email from ADMIN_EMAILS or demoting the role each revokes access.
    public function isAdmin(): bool
    {
        return $this->role === 'admin' && $this->isAllowlistedAdmin();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
