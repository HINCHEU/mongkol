<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venue extends Model
{
    // Saving touches the invitation, whose saved event clears its cached view.
    protected $touches = ['invitation'];

    protected $fillable = ['name', 'address', 'maps_url', 'lat', 'lng'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
