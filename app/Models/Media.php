<?php

namespace App\Models;

use App\Enums\MediaKind;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * An uploaded file. Images arrive on the private local disk (`path`, no variants yet); ProcessMedia
 * then writes clean WebP/PNG sizes to the media disk, fills `variants` and points `path` at the largest.
 */
class Media extends Model
{
    protected $table = 'media';

    // Saving touches the invitation, whose saved event clears its cached view.
    protected $touches = ['invitation'];

    protected $fillable = ['kind', 'path', 'variants', 'sort'];

    protected static function booted(): void
    {
        static::deleted(function (Media $media) {
            Storage::disk('local')->delete($media->path); // an original not processed yet
            self::disk()->delete([$media->path, ...array_values($media->variants ?? [])]);
        });
    }

    protected function casts(): array
    {
        return ['kind' => MediaKind::class, 'variants' => 'array'];
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(config('filesystems.media_disk'));
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /** Public URL of the size closest to `$width` (or the largest); null for an image still processing. */
    public function url(?int $width = null): ?string
    {
        if ($this->kind === MediaKind::Music) {
            return self::disk()->url($this->path);
        }
        if (! $this->variants) {
            return null;
        }

        $sizes = array_map('intval', array_keys($this->variants));
        sort($sizes);
        $pick = $width ? (collect($sizes)->first(fn ($size) => $size >= $width) ?? end($sizes)) : end($sizes);

        return self::disk()->url($this->variants[$pick]);
    }
}
