<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wish extends Model
{
    // Wishes show on the invitation page: saving touches the invitation, which clears its cached view.
    protected $touches = ['invitation'];

    protected $fillable = ['name', 'message'];

    protected function casts(): array
    {
        return ['is_hidden' => 'boolean'];
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
