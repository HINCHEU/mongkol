<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftAccount extends Model
{
    protected $fillable = ['owner', 'bank', 'account_name', 'account_number', 'khqr_media_id'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function khqr(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'khqr_media_id');
    }
}
