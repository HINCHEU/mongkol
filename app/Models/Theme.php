<?php

namespace App\Models;

use Database\Factories\ThemeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    /** @use HasFactory<ThemeFactory> */
    use HasFactory;

    protected $fillable = ['slug', 'name', 'version', 'tier', 'is_active', 'sort', 'thumbnail_path', 'manifest'];

    protected function casts(): array
    {
        return ['manifest' => 'array', 'is_active' => 'boolean'];
    }
}
