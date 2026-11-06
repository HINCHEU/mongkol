<?php

namespace App\Models;

use App\Enums\MediaKind;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $table = 'media';

    // Saving touches the invitation, whose saved event clears its cached view.
    protected $touches = ['invitation'];

    protected $fillable = ['kind', 'path', 'variants', 'sort'];

    protected function casts(): array
    {
        return ['kind' => MediaKind::class, 'variants' => 'array'];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
