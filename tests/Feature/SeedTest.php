<?php

use App\Enums\InvitationState;
use App\Models\EventPreset;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Setting;
use App\Services\LunarDate;
use Database\Seeders\ReferenceDataSeeder;

it('seeds a complete demo invitation', function () {
    $this->seed();

    $invitation = Invitation::where('slug', 'sokha-dara')->sole();

    expect($invitation->state())->toBe(InvitationState::Live)
        ->and($invitation->plan->slug)->toBe('premium')
        ->and($invitation->people)->toHaveCount(6)
        ->and($invitation->events)->toHaveCount(6)
        ->and($invitation->events->first()->venue)->not->toBeNull()
        ->and($invitation->guests)->toHaveCount(3)
        ->and($invitation->giftAccounts)->toHaveCount(1)
        ->and(LunarDate::for($invitation->wedding_date))->toContain('ខែមិគសិរ');
});

it('seeds plans, presets and trial settings', function () {
    $this->seed(ReferenceDataSeeder::class);

    expect(Plan::pluck('slug')->all())->toBe(['basic', 'premium'])
        ->and(EventPreset::count())->toBe(6)
        ->and(Setting::read('trial_days'))->toBe(2)
        ->and(Setting::read('trial_guest_cap'))->toBe(10)
        ->and(Setting::read('missing', 'fallback'))->toBe('fallback');
});

it('can run again without duplicating or overwriting admin edits', function () {
    $this->seed();
    Plan::where('slug', 'basic')->update(['price_usd_cents' => 999]);

    $this->seed();

    expect(Plan::count())->toBe(2)
        ->and(Plan::where('slug', 'basic')->value('price_usd_cents'))->toBe(999)
        ->and(Invitation::count())->toBe(1);
});
