<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public const MANUAL = 'manual_khqr';

    public const BAKONG = 'bakong';

    // Matches the column default, so a just-created payment reads as pending too.
    protected $attributes = ['status' => self::PENDING];

    // status, reviewed_by, reviewed_at and reference are set by the approve/reject actions only;
    // khqr, md5 and expires_at by Bakong::issue().
    protected $fillable = ['plan_id', 'amount_minor', 'currency', 'method', 'proof_media_id'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    /** A plan's list price in minor units: US cents, or riel (no decimals). */
    public static function price(Plan $plan, string $currency): int
    {
        return $currency === 'KHR' ? $plan->price_khr : $plan->price_usd_cents;
    }

    public static function format(int $minor, string $currency): string
    {
        return $currency === 'KHR' ? number_format($minor).'៛' : '$'.number_format($minor / 100, 2);
    }

    /** A Bakong QR that can no longer be paid. */
    public function expired(): bool
    {
        return $this->expires_at?->isPast() ?? false;
    }

    public function amount(): string
    {
        return self::format($this->amount_minor, $this->currency);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function proof(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'proof_media_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
