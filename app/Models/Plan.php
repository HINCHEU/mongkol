<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'slug', 'name', 'price_usd_cents', 'price_khr', 'max_guests', 'features', 'live_days_after_wedding', 'is_active',
    ];

    protected function casts(): array
    {
        return ['features' => 'array', 'is_active' => 'boolean'];
    }
}
