<?php

use App\Actions\CreateInvitation;
use App\Actions\ReviewPayment;
use App\Filament\Pages\Settings;
use App\Filament\Resources\EventPresets\Pages\ManageEventPresets;
use App\Filament\Resources\Invitations\Pages\ListInvitations;
use App\Filament\Resources\Plans\Pages\ManagePlans;
use App\Filament\Resources\Themes\Pages\ManageThemes;
use App\Filament\Widgets\SalesOverview;
use App\Models\ActivityLog;
use App\Models\EventPreset;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use App\Services\PlanLimits;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
    Notification::fake();
    config(['app.admin_emails' => ['admin@example.com']]);
    $this->admin = User::factory()->admin()->create(['email' => 'admin@example.com']);
    $this->actingAs($this->admin);
});

function couple(string $groom = 'Chan Sokha'): Invitation
{
    return app(CreateInvitation::class)->handle(User::factory()->create(), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => '2027-01-16',
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => $groom],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);
}

it('opens every admin page', function () {
    foreach (['/admin', '/admin/invitations', '/admin/payments', '/admin/plans', '/admin/themes', '/admin/event-presets', '/admin/settings', '/admin/activity-logs', '/admin/users'] as $page) {
        $this->get($page)->assertOk();
    }
});

it('filters invitations by state', function () {
    $trial = couple();
    $paused = couple('Sok Visal');
    $paused->forceFill(['trial_ends_at' => now()->subDay()])->save();

    Livewire::test(ListInvitations::class)
        ->filterTable('state', 'paused')
        ->assertCanSeeTableRecords([$paused])
        ->assertCanNotSeeTableRecords([$trial]);
});

it('extends a trial, moves live-until and suspends, logging each', function () {
    $invitation = couple();
    $invitation->forceFill(['trial_ends_at' => now()->subDay(), 'trial_reminded_at' => now()])->save();

    Livewire::test(ListInvitations::class)->callTableAction('extendTrial', $invitation, ['days' => 3])->assertHasNoTableActionErrors();
    $invitation->refresh();
    expect($invitation->state()->value)->toBe('trial')
        ->and($invitation->trial_ends_at->diffInHours(now()->addDays(3), absolute: true))->toBeLessThan(1)
        ->and($invitation->trial_reminded_at)->toBeNull();

    $invitation->forceFill(['paid_at' => now(), 'plan_id' => Plan::where('slug', 'basic')->value('id'), 'live_until' => now()->addMonth()])->save();
    Livewire::test(ListInvitations::class)->callTableAction('setLiveUntil', $invitation, ['live_until' => '2027-03-01 23:59:00']);
    expect($invitation->fresh()->live_until->format('Y-m-d H:i'))->toBe('2027-03-01 23:59');

    Livewire::test(ListInvitations::class)->callTableAction('suspend', $invitation);
    $this->get('/sokha-dara')->assertNotFound();
    Livewire::test(ListInvitations::class)->callTableAction('unsuspend', $invitation);
    $this->get('/sokha-dara')->assertOk();

    expect(ActivityLog::where('subject_id', $invitation->id)->pluck('action')->all())
        ->toBe(['invitation.trial_extended', 'invitation.live_until_set', 'invitation.suspended', 'invitation.unsuspended'])
        ->and(ActivityLog::first()->actor_id)->toBe($this->admin->id);
});

it('edits plans, themes and presets, and the app follows', function () {
    $basic = Plan::where('slug', 'basic')->first();
    Livewire::test(ManagePlans::class)->callTableAction('edit', $basic, [
        'name' => 'Basic', 'is_active' => true, 'price_usd_cents' => 1900, 'price_khr' => 76000, 'max_guests' => 250,
        'live_days_after_wedding' => 45, 'features' => [...$basic->features, 'gallery_photos' => 8],
    ])->assertHasNoTableActionErrors();

    $invitation = couple();
    $invitation->forceFill(['paid_at' => now(), 'plan_id' => $basic->id])->save();
    expect(PlanLimits::for($invitation->fresh())->galleryPhotos())->toBe(8)
        ->and(PlanLimits::for($invitation->fresh())->maxGuests())->toBe(250)
        ->and(ActivityLog::where('action', 'plan.updated')->sole()->changes)->toMatchArray(['price_usd_cents' => 1900]);

    $theme = Theme::where('slug', 'base')->first();
    Livewire::test(ManageThemes::class)->callTableAction('edit', $theme, ['name' => 'Morodok', 'tier' => 'premium', 'is_active' => true, 'sort' => 5]);
    expect($theme->fresh()->tier)->toBe('premium');

    Livewire::test(ManageEventPresets::class)->callAction('create', ['key' => 'tea', 'title_km' => 'ពិធីតែ', 'title_en' => 'Tea ceremony', 'default_sort' => 9, 'is_active' => true])
        ->assertHasNoActionErrors();
    expect(EventPreset::where('key', 'tea')->exists())->toBeTrue()
        ->and(ActivityLog::where('action', 'event_preset.created')->exists())->toBeTrue();
});

it('saves settings and logs only what changed', function () {
    Livewire::test(Settings::class)
        ->fillForm(['trial_days' => 3, 'trial_guest_cap' => 15, 'payment_instructions' => 'Pay with KW ref', 'telegram_chat_id' => ' 12345 '])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Setting::read('trial_days'))->toBe(3)
        ->and(Setting::read('trial_guest_cap'))->toBe(15)
        ->and(Setting::read('telegram_chat_id'))->toBe('12345')
        ->and(ActivityLog::where('action', 'settings.updated')->sole()->changes)->toHaveKeys(['trial_days', 'trial_guest_cap', 'payment_instructions', 'telegram_chat_id'])
        ->and(ActivityLog::where('action', 'settings.updated')->sole()->changes)->not->toHaveKey('payment_khqr');

    // New invitations follow the new trial length.
    expect(couple()->trial_ends_at->diffInHours(now()->addDays(3), absolute: true))->toBeLessThan(1);

    Livewire::test(Settings::class)->fillForm(['trial_days' => 0])->call('save')->assertHasFormErrors(['trial_days']);
});

it('shows the KHQR set in settings on the upgrade page', function () {
    Setting::updateOrCreate(['key' => 'payment_khqr'], ['value' => ['USD' => 'payment-khqr/usd.png', 'KHR' => 'https://example.com/khr.png']]);
    $invitation = couple();

    $this->actingAs($invitation->user)->get("/app/invitations/{$invitation->id}/upgrade")
        ->assertInertia(fn ($page) => $page->where('khqr.USD', url('storage/payment-khqr/usd.png'))->where('khqr.KHR', 'https://example.com/khr.png'));
});

it('sums this month’s approved payments per currency and logs reviews', function () {
    $invitation = couple();
    foreach ([['USD', 2900], ['KHR', 60000]] as [$currency, $amount]) {
        $payment = $invitation->payments()->make(['plan_id' => Plan::where('slug', 'basic')->value('id'), 'amount_minor' => $amount, 'currency' => $currency]);
        $payment->user()->associate($invitation->user)->save();
        $payment->forceFill(['reference' => 'KW-'.$payment->id])->save();
        ReviewPayment::approve($payment, $this->admin);
        $invitation->forceFill(['paid_at' => null])->save(); // let the second one approve as a first payment too
    }

    Livewire::test(SalesOverview::class)->assertSee('$29.00')->assertSee('60,000៛');
    expect(ActivityLog::where('action', 'payment.approved')->count())->toBe(2);
});
