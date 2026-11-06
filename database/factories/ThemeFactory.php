<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Theme>
 */
class ThemeFactory extends Factory
{
    public function definition(): array
    {
        $slug = fake()->unique()->slug(2);

        return [
            'slug' => $slug,
            'name' => str($slug)->headline(),
            'version' => '1.0.0',
            'tier' => 'standard',
            'is_active' => true,
            'manifest' => ['slug' => $slug, 'extends' => 'base', 'sections' => []],
        ];
    }
}
