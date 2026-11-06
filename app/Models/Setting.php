<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    public static function read(string $key, mixed $default = null): mixed
    {
        return static::firstWhere('key', $key)?->value ?? $default;
    }

    /** An image setting (e.g. a KHQR): a media-disk path from the admin uploader, or a full URL set by hand. */
    public static function imageUrl(?string $value): ?string
    {
        return match (true) {
            blank($value) => null,
            str_starts_with($value, 'http') => $value,
            default => Media::disk()->url($value),
        };
    }
}
