<?php

use App\Actions\CreateInvitation;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use App\Models\Wish;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Js;
use Inertia\Testing\AssertableInertia;
use Spatie\SimpleExcel\SimpleExcelReader;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
});

function replying(): Invitation
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

it('takes one reply per personal link and changes it on a second answer', function () {
    $invitation = replying();

    $this->postJson('/sokha-dara/k7Px2Q/rsvp', ['attending' => true, 'party_size' => 2, 'name' => 'Someone else'])->assertOk();
    $this->postJson('/sokha-dara/k7Px2Q/rsvp', ['attending' => true, 'party_size' => 3, 'message' => 'See you!'])->assertOk();

    $rsvp = $invitation->rsvps()->sole();
    expect($rsvp)->name->toBe('លោក ចាន់ ថា និងភរិយា')->party_size->toBe(3)->message->toBe('See you!')
        ->and($rsvp->guest_id)->toBe($invitation->guests()->value('id'));

    // The form shows their reply when they come back.
    $this->get('/sokha-dara/k7Px2Q')->assertSee(Js::from(['attending' => true, 'party_size' => 3, 'message' => 'See you!']), false);
});

it('asks the generic link for a name, and checks the answer', function () {
    $invitation = replying();

    $this->postJson('/sokha-dara/rsvp', ['attending' => true, 'party_size' => 1])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/sokha-dara/rsvp', ['name' => 'Dara', 'attending' => true])->assertJsonValidationErrors('party_size');
    $this->postJson('/sokha-dara/rsvp', ['name' => 'Dara', 'attending' => true, 'party_size' => 50])->assertJsonValidationErrors('party_size');
    $this->postJson('/sokha-dara/rsvp', ['name' => 'Vanna', 'attending' => false])->assertOk();

    expect($invitation->rsvps()->sole())->name->toBe('Vanna')->attending->toBeFalse()->party_size->toBe(0);
});

it('quietly drops what fills the honeypot, and limits a flood', function () {
    $invitation = replying();

    $this->postJson('/sokha-dara/wishes', ['name' => 'Bot', 'message' => 'Buy now', 'website' => 'spam.example'])->assertOk();
    expect($invitation->wishes()->count())->toBe(0);

    foreach (range(1, 9) as $i) {
        $this->postJson('/sokha-dara/wishes', ['name' => "Guest {$i}", 'message' => 'Congratulations'])->assertOk();
    }
    $this->postJson('/sokha-dara/wishes', ['name' => 'Too many', 'message' => 'Hi'])->assertTooManyRequests();
});

it('shows wishes on the invitation as plain text, newest first', function () {
    replying();

    $this->postJson('/sokha-dara/wishes', ['name' => 'Dara', 'message' => '<script>alert(1)</script> Congratulations!'])->assertOk()->assertJson(['shown' => true]);
    $this->postJson('/sokha-dara/k7Px2Q/wishes', ['message' => 'សូមជូនពរ'])->assertOk();

    $page = $this->get('/sokha-dara')->assertOk();
    $page->assertDontSee('<script>alert(1)</script>', false); // only ever JSON-encoded into x-data, shown with x-text
    $wishes = Wish::latest('id')->pluck('name')->all();
    expect($wishes)->toBe(['លោក ចាន់ ថា និងភរិយា', 'Dara']);
});

it('holds wishes back when the couple approves them first, and closes forms they turn off', function () {
    $invitation = replying();
    $this->actingAs($invitation->user)->put("/app/invitations/{$invitation->id}/responses/settings", [
        'rsvp_enabled' => false, 'wishes_enabled' => true, 'wishes_need_approval' => true,
    ])->assertSessionHasNoErrors();

    $this->postJson('/sokha-dara/wishes', ['name' => 'Dara', 'message' => 'Congratulations'])->assertOk()->assertJson(['shown' => false]);
    $this->get('/sokha-dara')->assertDontSee('Congratulations')->assertDontSee('id="rsvp"', false);
    $this->postJson('/sokha-dara/rsvp', ['name' => 'Dara', 'attending' => false])->assertNotFound();

    $wish = $invitation->wishes()->sole();
    $this->put("/app/invitations/{$invitation->id}/wishes/{$wish->id}", ['hidden' => false]);
    $this->get('/sokha-dara')->assertSee('Congratulations');
});

it('accepts nothing once the invitation is paused', function () {
    $invitation = replying();
    $invitation->forceFill(['trial_ends_at' => now()->subMinute()])->save();

    $this->postJson('/sokha-dara/rsvp', ['name' => 'Dara', 'attending' => false])->assertNotFound();
    $this->postJson('/sokha-dara/wishes', ['name' => 'Dara', 'message' => 'Hi'])->assertNotFound();
});

it('totals replies for the couple and exports them', function () {
    $invitation = replying();
    $invitation->guests()->create(['name' => 'No answer yet']);
    $this->postJson('/sokha-dara/k7Px2Q/rsvp', ['attending' => true, 'party_size' => 2]);
    $this->postJson('/sokha-dara/rsvp', ['name' => 'Vanna', 'attending' => true, 'party_size' => 4]);
    $this->postJson('/sokha-dara/rsvp', ['name' => 'Sok', 'attending' => false]);
    $this->postJson('/sokha-dara/wishes', ['name' => 'Dara', 'message' => 'Congratulations']);

    $this->actingAs($invitation->user)->get("/app/invitations/{$invitation->id}/responses")
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Invitations/Responses', false)
            ->where('totals', ['replies' => 3, 'attending' => 2, 'people' => 6, 'declined' => 1, 'waiting' => 1])
            ->has('rsvps', 3)->has('wishes', 1));

    $file = $this->get("/app/invitations/{$invitation->id}/responses/export")->assertOk()->baseResponse->getFile()->getPathname();
    expect(SimpleExcelReader::create($file, 'xlsx')->getRows()->pluck('name')->all())->toContain('Vanna', 'Sok');

    $this->actingAs(User::factory()->create())->get("/app/invitations/{$invitation->id}/responses")->assertForbidden();
});
