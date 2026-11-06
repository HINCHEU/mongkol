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

    // Honorifics offered in the title dropdown and recognised at the start of a pasted name.
    public const TITLES = ['លោក', 'លោកស្រី', 'អ្នកស្រី', 'កញ្ញា', 'ឯកឧត្តម', 'លោកជំទាវ', 'អ្នកឧកញ៉ា', 'សម្ដេច', 'ព្រះតេជព្រះគុណ'];

    // Recognised at the end of a pasted name.
    public const SUFFIXES = ['និងភរិយា', 'និងស្វាមី', 'និងក្រុមគ្រួសារ', 'និងបុត្រ'];

    // code, sent_at and the open counters are set by the app, not by the couple's form.
    protected $fillable = ['title', 'name', 'suffix', 'side', 'group', 'phone'];

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            $guest->code ??= self::newCode($guest->invitation_id);
        });
    }

    protected function casts(): array
    {
        return [
            'side' => GuestSide::class,
            'sent_at' => 'datetime',
            'first_opened_at' => 'datetime',
            'last_opened_at' => 'datetime',
        ];
    }

    public static function newCode(int $invitationId): string
    {
        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)];
            }
        } while (self::where('invitation_id', $invitationId)->where('code', $code)->exists());

        return $code;
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /** "លោក ចាន់ ថា និងភរិយា": what the envelope, the link preview and the share message show. */
    public function displayName(): string
    {
        return trim(implode(' ', array_filter([$this->title, $this->name, $this->suffix])));
    }

    public function url(): string
    {
        return url($this->invitation->slug.'/'.$this->code);
    }
}
