<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'theme_id' => Theme::factory(),
            'slug' => fake()->unique()->slug(2),
            'wedding_date' => now()->addMonths(2)->toDateString(),
            'trial_ends_at' => now()->addDays(2),
        ];
    }

    public function paused(): static
    {
        return $this->state(fn () => ['trial_ends_at' => now()->subDay()]);
    }

    public function live(): static
    {
        return $this->state(fn () => [
            'trial_ends_at' => now()->subDays(5),
            'paid_at' => now()->subDays(4),
            'live_until' => now()->addMonths(3),
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn () => [
            'trial_ends_at' => now()->subMonths(4),
            'paid_at' => now()->subMonths(4),
            'live_until' => now()->subDay(),
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['suspended_at' => now()]);
    }
}
