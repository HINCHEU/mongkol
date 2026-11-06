<?php

namespace App\Models;

use App\Enums\InvitationState;
use App\Services\Themes\InvitationViewModel;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory, SoftDeletes;

    // Couple-editable only. user_id, plan_id and the four state timestamps are set by actions, never from a form.
    protected $fillable = [
        'slug',
        'theme_id',
        'theme_settings',
        'content',
        'wedding_date',
    ];

    protected static function booted(): void
    {
        // Child models touch the invitation on save, so this also covers people, events, venues, media and gifts.
        $forget = fn (Invitation $invitation) => Cache::forget(InvitationViewModel::cacheKey($invitation->id));
        static::saved($forget);
        static::deleted($forget);
    }

    protected function casts(): array
    {
        return [
            'theme_settings' => 'array',
            'content' => 'array',
            'wedding_date' => 'date',
            'trial_ends_at' => 'datetime',
            'paid_at' => 'datetime',
            'live_until' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Computed, not stored, so a late cron job can never leave a link in the wrong state.
     * Keep in step with the scopes below.
     */
    public function state(): InvitationState
    {
        if ($this->suspended_at !== null) {
            return InvitationState::Suspended;
        }

        if ($this->paid_at !== null) {
            return $this->live_until !== null && ! $this->live_until->isFuture()
                ? InvitationState::Ended
                : InvitationState::Live;
        }

        return $this->trial_ends_at->isFuture() ? InvitationState::Trial : InvitationState::Paused;
    }

    public function scopeTrial(Builder $query): void
    {
        $query->whereNull('suspended_at')->whereNull('paid_at')->where('trial_ends_at', '>', now());
    }

    public function scopePaused(Builder $query): void
    {
        $query->whereNull('suspended_at')->whereNull('paid_at')->where('trial_ends_at', '<=', now());
    }

    public function scopeLive(Builder $query): void
    {
        $query->whereNull('suspended_at')->whereNotNull('paid_at')
            ->where(fn (Builder $q) => $q->whereNull('live_until')->orWhere('live_until', '>', now()));
    }

    public function scopeEnded(Builder $query): void
    {
        $query->whereNull('suspended_at')->whereNotNull('paid_at')->where('live_until', '<=', now());
    }

    public function scopeSuspended(Builder $query): void
    {
        $query->whereNotNull('suspended_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(InvitationPerson::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class)->orderBy('sort');
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class)->orderBy('sort');
    }

    public function giftAccounts(): HasMany
    {
        return $this->hasMany(GiftAccount::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
