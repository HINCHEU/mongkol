<?php

namespace App\Actions;

use App\Models\Invitation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateInvitation
{
    /** Paths the public /{slug} route must never swallow. */
    public const RESERVED_SLUGS = ['app', 'admin', 'api', 'auth', 'themes', 'login', 'logout', 'up', 'build', 'storage', 'livewire', 'filament', 'css', 'js', 'fonts', 'privacy', 'terms', 'refunds'];

    /**
     * @param  array{theme_id: int, wedding_date: string, groom: array{name_km: string, name_en: string}, bride: array{name_km: string, name_en: string}}  $data
     */
    public function handle(User $user, array $data): Invitation
    {
        return DB::transaction(function () use ($user, $data) {
            $invitation = new Invitation([
                'slug' => $this->slugFor($data['groom']['name_en'], $data['bride']['name_en']),
                'theme_id' => $data['theme_id'],
                'wedding_date' => $data['wedding_date'],
            ]);
            $invitation->forceFill([
                'user_id' => $user->id,
                'trial_ends_at' => now()->addDays((int) Setting::read('trial_days', 2)),
            ])->save();

            $invitation->people()->createMany([
                ['role' => 'groom', ...$data['groom']],
                ['role' => 'bride', ...$data['bride']],
            ]);

            return $invitation;
        });
    }

    /** sokha-dara from "Chan Sokha" and "Ly Dara": Khmer names put the given name last. */
    public function slugFor(string $groom, string $bride): string
    {
        $given = fn (string $name) => Str::slug(Str::afterLast(trim($name), ' '));
        $base = trim($given($groom).'-'.$given($bride), '-') ?: 'wedding';

        $slug = $base;
        for ($n = 2; in_array($slug, self::RESERVED_SLUGS, true) || Invitation::withTrashed()->where('slug', $slug)->exists(); $n++) {
            $slug = "{$base}-{$n}";
        }

        return $slug;
    }
}
