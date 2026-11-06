<?php

namespace App\Models;

use App\Enums\GuestSide;
use Database\Factories\GuestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    /** @use HasFactory<GuestFactory> */
    use HasFactory;

    // No look-alikes (0, O, 1, I, l): 57 characters, so 6 of them give about 34 billion codes.
    public const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';

    // code, sent_at and the open counters are set by the app, not by the couple's form.
    protected $fillable = ['title', 'name', 'suffix', 'side', 'group', 'phone'];

    protected function casts(): array
    {
        return [
            'side' => GuestSide::class,
            'sent_at' => 'datetime',
            'first_opened_at' => 'datetime',
            'last_opened_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
