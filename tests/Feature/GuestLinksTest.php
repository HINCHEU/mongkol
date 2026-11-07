<?php

use App\Actions\CreateInvitation;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Js;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
});

function linked(): Invitation
{
    $invitation = app(CreateInvitation::class)->handle(User::factory()->create(), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => now()->addMonths(2)->toDateString(),
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);
    $invitation->guests()->make(['title' => 'លោក', 'name' => 'ចាន់ ថា', 'suffix' => 'និងភរិយា'])->forceFill(['code' => 'k7Px2Q'])->save();

    return $invitation;
}

it('shows the generic link to an honoured guest, with a preview card and no indexing', function () {
    linked();

    $this->get('/sokha-dara')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertSee('<meta property="og:title" content="សូមគោរពអញ្ជើញ ភ្ញៀវកិត្តិយស">', false)
        ->assertSee('<meta property="og:url" content="'.url('sokha-dara').'">', false)
        ->assertSee('has-ribbon', false) // still in the trial
        ->assertDontSee('/opened', false);
});

it('names the guest on their personal link and in its preview card', function () {
    linked();

    $this->get('/sokha-dara/k7Px2Q')
        ->assertOk()
        ->assertSee('<meta property="og:title" content="សូមគោរពអញ្ជើញ លោក ចាន់ ថា និងភរិយា">', false)
        ->assertSee('លោក ចាន់ ថា និងភរិយា')
        ->assertSee(Js::from(url('sokha-dara/k7Px2Q/opened')), false);

    $this->get('/sokha-dara/K7PX2Q')->assertNotFound(); // codes are case-sensitive
    $this->get('/sokha-dara/zzzzzz')->assertNotFound();
    $this->get('/nobody-here')->assertNotFound();
});

it('counts an open from the envelope beacon only, without a CSRF token', function () {
    $invitation = linked();
    $guest = $invitation->guests()->sole();

    $this->get('/sokha-dara/k7Px2Q');
    expect($guest->fresh()->open_count)->toBe(0); // page loads (and preview bots) don't count

    $this->post('/sokha-dara/k7Px2Q/opened')->assertNoContent();
    $first = $guest->fresh()->first_opened_at;
    $this->travel(1)->hours();
    $this->post('/sokha-dara/k7Px2Q/opened')->assertNoContent();

    expect($guest->fresh())->open_count->toBe(2)
        ->first_opened_at->toEqual($first)
        ->last_opened_at->greaterThan($first)->toBeTrue();
});

it('pauses after the trial, ends after the live period, and hides suspended invitations', function () {
    $invitation = linked();

    $invitation->forceFill(['trial_ends_at' => now()->subMinute()])->save();
    $this->get('/sokha-dara/k7Px2Q')->assertOk()->assertSee('isn’t available', false)->assertDontSee('ចាន់ ថា');

    $invitation->forceFill(['paid_at' => now()->subMonth(), 'live_until' => now()->subDay()])->save();
    $this->get('/sokha-dara')->assertOk()->assertSee('The wedding has passed');

    $invitation->forceFill(['live_until' => now()->addMonth()])->save();
    $this->get('/sokha-dara')->assertOk()->assertDontSee('has-ribbon', false);

    $invitation->forceFill(['suspended_at' => now()])->save();
    $this->get('/sokha-dara')->assertNotFound();
});

it('serves a QR code for the personal link and the calendar file', function () {
    linked();

    $this->get('/sokha-dara/k7Px2Q/qr.svg')->assertOk()->assertHeader('Content-Type', 'image/svg+xml');
    expect($this->get('/sokha-dara/k7Px2Q/qr.svg')->getContent())->toStartWith('<?xml');
    $this->get('/sokha-dara/calendar.ics')->assertOk()->assertSee('BEGIN:VCALENDAR');
});

it('leaves the app’s own routes alone', function () {
    linked();

    $this->get('/themes')->assertOk();
    $this->get('/login')->assertOk();
    $this->get('/app')->assertRedirect('/login');
    $this->get('/up')->assertOk();
});

it('unlocks the share page once there is a cover, and saves the message', function () {
    $invitation = linked();
    $this->actingAs($invitation->user);

    $this->get("/app/invitations/{$invitation->id}/share")
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Invitations/Share', false)
            ->where('hasCover', false)
            ->where('guests.0.name', 'លោក ចាន់ ថា និងភរិយា')
            ->where('guests.0.url', url('sokha-dara/k7Px2Q')));

    $invitation->media()->create(['kind' => 'cover', 'path' => 'c.webp', 'variants' => [1600 => 'c.webp']]);
    $this->put("/app/invitations/{$invitation->id}/share", ['message' => 'Hi {name}: {link}'])->assertSessionHasNoErrors();

    $this->get("/app/invitations/{$invitation->id}/share")
        ->assertInertia(fn (AssertableInertia $page) => $page->where('hasCover', true)->where('message', 'Hi {name}: {link}'));

    $this->actingAs(User::factory()->create())->get("/app/invitations/{$invitation->id}/share")->assertForbidden();
});

it('tells the couple who opened their link and when', function () {
    $invitation = linked();
    $page = fn () => $this->actingAs($invitation->user)->get("/app/invitations/{$invitation->id}/share");

    $page()->assertInertia(fn (AssertableInertia $p) => $p->where('guests.0.open_count', 0)->where('guests.0.last_opened', null));

    $this->travelTo(now()->subHours(3), fn () => $this->post('/sokha-dara/k7Px2Q/opened')->assertNoContent());

    $page()->assertInertia(fn (AssertableInertia $p) => $p->where('guests.0.open_count', 1)->where('guests.0.last_opened', '3 hours ago'));
});
