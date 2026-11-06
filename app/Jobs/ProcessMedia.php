<?php

namespace App\Jobs;

use App\Enums\MediaKind;
use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

/**
 * Re-encodes an uploaded image into the public sizes and deletes the original. Re-encoding with GD
 * drops all metadata, so EXIF (camera, GPS location) never reaches the public disk.
 */
class ProcessMedia implements ShouldQueue
{
    use Queueable;

    // Widest first: each size is scaled down from the previous one.
    public const WIDTHS = [1600, 960, 480];

    public function __construct(public Media $media) {}

    public function handle(): void
    {
        $original = $this->media->path;
        // Reading auto-rotates by the EXIF orientation before it is dropped.
        $image = ImageManager::gd()->read(Storage::disk('local')->path($original));
        $base = "invitations/{$this->media->invitation_id}/".Str::random(24);
        $variants = [];

        if ($this->media->kind === MediaKind::Khqr) {
            // QR codes stay lossless and sharp; one size is plenty for a phone screen.
            $variants[960] = "{$base}.png";
            Media::disk()->put($variants[960], (string) $image->scaleDown(width: 960)->toPng(), 'public');
        } else {
            foreach (self::WIDTHS as $width) {
                $variants[$width] = "{$base}-{$width}.webp";
                Media::disk()->put($variants[$width], (string) $image->scaleDown(width: $width)->toWebp(quality: 82), 'public');
            }
        }

        $this->media->update(['path' => reset($variants), 'variants' => $variants]);
        Storage::disk('local')->delete($original);
    }
}
