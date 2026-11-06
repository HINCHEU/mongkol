<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPreset extends Model
{
    protected $fillable = ['key', 'title_km', 'title_en', 'default_sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
