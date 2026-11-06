<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationPerson extends Model
{
    public const ROLES = ['groom', 'bride', 'groom_father', 'groom_mother', 'bride_father', 'bride_mother'];

    protected $table = 'invitation_people';

    protected $fillable = ['role', 'title', 'name_km', 'name_en', 'is_late', 'photo_media_id'];

    protected function casts(): array
    {
        return ['is_late' => 'boolean'];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }
}
