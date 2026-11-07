<?php

use App\Actions\CreateInvitation;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Theme;
use App\Models\User;
use App\Services\PlanLimits;
use App\Services\Themes\ThemeRenderer;
use Database\Seeders\ReferenceDataSeeder;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
});

function styled(): Invitation
{
    $invitation = app(CreateInvitation::class)->handle(User::factory()->create(), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => now()->addMonths(2)->toDateString(),
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);
    test()->actingAs($invitation->user);

    return $invitation;
}

it('orders and hides sections, keeping the cover first and unplaced ones at the end', function () {
    expect(ThemeRenderer::arrange(['cover', 'invitation', 'date', 'program', 'gift'], ['order' => ['gift', 'date', 'cover'], 'hidden' => ['program', 'cover']]))
        ->toBe(['cover', 'gift', 'date', 'invitation'])
        ->and(ThemeRenderer::arrange(['cover', 'invitation', 'date'], []))->toBe(['cover', 'invitation', 'date']);
});

it('lets a Premium or trial couple pick colours, fonts and the section order', function () {
    $invitation = styled();
    $base = Theme::where('slug', 'base')->value('id');

    $this->get("/app/invitations/{$invitation->id}/edit/theme")->assertInertia(fn (AssertableInertia $page) => $page
        ->where('invitation.can_customize', true)
        ->where('themes.0.options.palette.choices', ['crimson', 'jade', 'sapphire'])
        ->where('themes.0.sections.0', 'cover'));

    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $base, 'theme_settings' => [
        'palette' => 'jade', 'font' => 'traditional', 'show_countdown' => false, 'order' => ['date', 'invitation'],
    ]])->assertSessionHasNoErrors();

    $this->get('/sokha-dara')
        ->assertSee('data-palette="jade" data-font="traditional"', false)
        ->assertSee('family=Moulpali', false)
        ->assertSeeInOrder(['id="cover"', 'id="date"', 'id="invitation"'], false);

    $this->get('/sokha-dara')->assertDontSee('class="petals"', false); // off unless the couple turns them on

    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $base, 'theme_settings' => ['hidden' => ['invitation'], 'petals' => true]]);
    $this->get('/sokha-dara')->assertSee('data-palette="crimson" data-font="classic"', false)->assertDontSee('id="invitation"', false)
        ->assertSee('<canvas class="petals" aria-hidden="true"></canvas>', false);
});

it('accepts only what the chosen theme offers', function () {
    $invitation = styled();
    $angkor = Theme::where('slug', 'angkor-gold')->firstOrFail();

    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $angkor->id, 'theme_settings' => ['palette' => 'jade']])
        ->assertSessionHasErrors('theme_settings.palette'); // Angkor Gold keeps its own colours
    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $angkor->id, 'theme_settings' => ['hidden' => ['cover'], 'order' => ['nope'], 'extra' => 1]])
        ->assertSessionHasErrors(['theme_settings', 'theme_settings.hidden.0', 'theme_settings.order.0']);
});

it('keeps the options of a Basic invitation saved but not shown, and warns before Basic checkout', function () {
    $invitation = styled();
    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $invitation->theme_id, 'theme_settings' => ['palette' => 'sapphire']]);
    expect(PlanLimits::for($invitation->fresh())->beyond(Plan::where('slug', 'basic')->first()))->toContain('Theme options (colours, fonts, sections)');

    $invitation->forceFill(['paid_at' => now(), 'plan_id' => Plan::where('slug', 'basic')->value('id'), 'live_until' => now()->addMonths(3)])->save();

    $this->get('/sokha-dara')->assertSee('data-palette="crimson"', false);
    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $invitation->theme_id, 'theme_settings' => ['palette' => 'jade']])
        ->assertSessionHasErrors('theme_settings');
    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $invitation->theme_id])->assertSessionHasNoErrors(); // switching still works
    expect($invitation->fresh()->theme_settings)->toBe(['palette' => 'sapphire']);
});
