<?php

namespace App\Services;

use App\Enums\MediaKind;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Setting;

/**
 * What an invitation may use. A paid invitation gets its plan's limits; an unpaid one (trial or
 * paused) gets Premium's, so couples try everything, except personal links, capped at trial_guest_cap.
 * Limits live in plans.max_guests and plans.features, editable in admin without a deploy.
 */
final class PlanLimits
{
    // Even "unlimited" has a ceiling, against abuse and runaway imports.
    public const GUEST_CEILING = 5000;

    private function __construct(private readonly Invitation $invitation, private readonly ?Plan $plan) {}

    public static function for(Invitation $invitation): self
    {
        $plan = $invitation->paid_at !== null && $invitation->plan_id !== null
            ? $invitation->plan
            : once(fn () => Plan::where('slug', 'premium')->first());

        return new self($invitation, $plan);
    }

    public function isPaid(): bool
    {
        return $this->invitation->paid_at !== null;
    }

    public function maxGuests(): int
    {
        if (! $this->isPaid()) {
            return (int) Setting::read('trial_guest_cap', 10);
        }

        return min($this->plan?->max_guests ?? self::GUEST_CEILING, self::GUEST_CEILING);
    }

    public function galleryPhotos(): int
    {
        return (int) ($this->plan?->features['gallery_photos'] ?? 0);
    }

    /** own_music, video, custom_slug, premium_themes, hide_credit */
    public function allows(string $feature): bool
    {
        return (bool) ($this->plan?->features[$feature] ?? false);
    }

    /**
     * What this invitation uses that `$plan` doesn't include, for the checkout warning.
     *
     * @return list<string>
     */
    public function beyond(Plan $plan): array
    {
        $features = $plan->features;
        $photos = $this->invitation->media()->where('kind', MediaKind::Gallery)->count();
        $guests = $this->invitation->guests()->count();

        return array_values(array_filter([
            $photos > ($features['gallery_photos'] ?? 0) ? "{$photos} gallery photos ({$plan->name} shows {$features['gallery_photos']})" : null,
            $plan->max_guests !== null && $guests > $plan->max_guests ? "{$guests} guests ({$plan->name} allows {$plan->max_guests})" : null,
            empty($features['own_music']) && $this->invitation->media()->where('kind', MediaKind::Music)->exists() ? 'Your own music' : null,
            empty($features['video']) && ! empty($this->invitation->content['video_url']) ? 'YouTube video' : null,
            empty($features['premium_themes']) && $this->invitation->theme?->tier === 'premium' ? "Premium theme ({$this->invitation->theme->name})" : null,
        ]));
    }

    /** The theme the page renders with: a Premium theme on a plan without it falls back to base. */
    public function themeSlug(): string
    {
        $theme = $this->invitation->theme;

        return $theme->tier === 'premium' && ! $this->allows('premium_themes') ? 'base' : $theme->slug;
    }
}
