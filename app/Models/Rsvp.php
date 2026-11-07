<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// No $touches: replies don't change the invitation page, so its cached view stays.
class Rsvp extends Model
{
    protected $fillable = ['name', 'attending', 'party_size', 'message'];

    protected function casts(): array
    {
        return ['attending' => 'boolean'];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
