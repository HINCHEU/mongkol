<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftAccount extends Model
{
    // Saving touches the invitation, whose saved event clears its cached view.
    protected $touches = ['invitation'];

    protected $fillable = ['owner', 'bank', 'account_name', 'account_number', 'khqr_media_id'];

    protected static function booted(): void
    {
        // The KHQR image belongs to this account only; the column alone would just be nulled.
        static::deleted(fn (GiftAccount $account) => $account->khqr?->delete());
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function khqr(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'khqr_media_id');
    }
}
