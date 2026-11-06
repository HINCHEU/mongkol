<?php

namespace Database\Factories;

use App\Enums\GuestSide;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'code' => fake()->unique()->regexify('['.Guest::CODE_ALPHABET.']{6}'),
            'title' => 'លោក',
            'name' => fake()->name(),
            'side' => fake()->randomElement(GuestSide::cases()),
        ];
    }
}
