<?php

use App\Actions\CreateInvitation;
use App\Enums\InvitationState;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class); // plans, presets, trial settings and the base theme
    $this->withoutVite();
});

function editable(): Invitation
{
    return app(CreateInvitation::class)->handle(User::factory()->create(), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => now()->addMonths(2)->toDateString(),
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);
}

it('creates an invitation in trial with the couple and a slug from their given names', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/app/invitations', [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => now()->addMonths(2)->toDateString(),
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ])->assertRedirect();

    $invitation = $user->invitations()->sole();
    expect($invitation->slug)->toBe('sokha-dara')
        ->and($invitation->state())->toBe(InvitationState::Trial)
        ->and($invitation->trial_ends_at->diffInHours(now()->addDays(2), absolute: true))->toBeLessThan(1)
        ->and($invitation->people()->pluck('role')->sort()->values()->all())->toBe(['bride', 'groom']);
});

it('numbers taken slugs and skips reserved words', function () {
    $create = app(CreateInvitation::class);
    Invitation::factory()->create(['slug' => 'sokha-dara']);
    Invitation::factory()->create(['slug' => 'sokha-dara-2'])->delete(); // soft-deleted still holds its link

    expect($create->slugFor('Chan Sokha', 'Ly Dara'))->toBe('sokha-dara-3')
        ->and($create->slugFor('', 'Admin'))->toBe('admin-2')
        ->and($create->slugFor('ស', 'ដ'))->toBe('wedding');
});

it('validates the create form', function () {
    $this->actingAs(User::factory()->create())->post('/app/invitations', [
        'wedding_date' => now()->subDay()->toDateString(),
        'groom' => ['name_km' => '', 'name_en' => 'ស'],
    ])->assertSessionHasErrors(['theme_id', 'wedding_date', 'groom.name_km', 'groom.name_en', 'bride.name_km']);
});

it('lists only the user’s own invitations', function () {
    $mine = editable();
    editable(); // someone else's

    $this->actingAs($mine->user)->get('/app')
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Home', false)
            ->has('invitations', 1)->where('invitations.0.slug', 'sokha-dara'));
});

it('opens every editor step for the owner and refuses everyone else', function () {
    $invitation = editable();

    foreach (['couple', 'venues', 'program', 'photos', 'gift', 'wording', 'theme'] as $step) {
        $this->actingAs($invitation->user)->get("/app/invitations/{$invitation->id}/edit/{$step}")
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Invitations/Edit', false)->where('step', $step));
    }

    $stranger = User::factory()->create();
    $this->actingAs($stranger)->get("/app/invitations/{$invitation->id}/edit")->assertForbidden();
    $this->actingAs($stranger)->get("/app/invitations/{$invitation->id}/preview")->assertForbidden();
    $this->actingAs($stranger)->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $invitation->theme_id])->assertForbidden();
});

it('saves the couple and parents, and removes an emptied parent', function () {
    $invitation = editable();
    $people = fn (array $father) => ['people' => [
        'groom' => ['name_km' => 'ចាន់ សុខុម', 'name_en' => 'Chan Sokhom'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
        'groom_father' => $father,
    ]];

    $this->actingAs($invitation->user)
        ->put("/app/invitations/{$invitation->id}/couple", $people(['title' => 'លោក', 'name_km' => 'ចាន់ វណ្ណា', 'is_late' => true]))
        ->assertSessionHasNoErrors();

    expect($invitation->people()->where('role', 'groom')->value('name_km'))->toBe('ចាន់ សុខុម')
        ->and($invitation->people()->where('role', 'groom_father')->first())->is_late->toBeTrue()->title->toBe('លោក');

    $this->put("/app/invitations/{$invitation->id}/couple", $people(['name_km' => '']));
    expect($invitation->people()->where('role', 'groom_father')->exists())->toBeFalse();

    $this->put("/app/invitations/{$invitation->id}/couple", ['people' => ['groom' => ['name_km' => '']]])
        ->assertSessionHasErrors(['people.groom.name_km', 'people.bride.name_km']);
});

it('syncs venues: updates, creates, deletes and keeps order', function () {
    $invitation = editable();
    $put = fn (array $venues) => $this->actingAs($invitation->user)->put("/app/invitations/{$invitation->id}/venues", ['venues' => $venues]);

    $put([['name' => 'Home'], ['name' => 'Hall', 'maps_url' => 'https://maps.google.com/?q=1,2']])->assertSessionHasNoErrors();
    [$home, $hall] = $invitation->venues()->orderBy('id')->get();

    $put([['id' => $hall->id, 'name' => 'Grand Hall'], ['name' => 'Pagoda']])->assertSessionHasNoErrors();

    expect($invitation->venues()->orderBy('id')->pluck('name')->all())->toBe(['Grand Hall', 'Pagoda'])
        ->and($home->fresh())->toBeNull();

    $put([['name' => 'x', 'maps_url' => 'javascript:alert(1)']])->assertSessionHasErrors('venues.0.maps_url');
});

it('refuses rows that belong to another invitation', function () {
    $invitation = editable();
    $other = editable();
    $foreign = $other->venues()->create(['name' => 'Not yours']);

    $this->actingAs($invitation->user)
        ->put("/app/invitations/{$invitation->id}/venues", ['venues' => [['id' => $foreign->id, 'name' => 'Mine now']]])
        ->assertSessionHasErrors('venues.0.id');

    expect($foreign->fresh()->name)->toBe('Not yours');
});

it('saves the program in Phnom Penh time, in the given order', function () {
    $invitation = editable();
    $venue = $invitation->venues()->create(['name' => 'Home']);

    $this->actingAs($invitation->user)->put("/app/invitations/{$invitation->id}/program", [
        'wedding_date' => '2026-12-12',
        'events' => [
            ['preset_key' => 'reception', 'title_km' => 'ពិធីជប់លៀង', 'date' => '2026-12-12', 'time' => '17:00', 'venue_id' => $venue->id],
            ['title_km' => 'ពិធីផ្ទាល់ខ្លួន', 'date' => '2026-12-12', 'time' => '07:00'],
        ],
    ])->assertSessionHasNoErrors();

    $events = $invitation->events()->get();
    expect($invitation->fresh()->wedding_date->toDateString())->toBe('2026-12-12')
        ->and($events->pluck('title_km')->all())->toBe(['ពិធីជប់លៀង', 'ពិធីផ្ទាល់ខ្លួន'])
        ->and($events[0]->starts_at->utc()->format('Y-m-d H:i'))->toBe('2026-12-12 10:00')
        ->and($events[0]->venue_id)->toBe($venue->id);
});

it('saves gift accounts, wording and theme without losing other content', function () {
    $invitation = editable();
    $invitation->update(['content' => ['video_url' => 'https://youtu.be/abcdefghijk']]);
    $this->actingAs($invitation->user);

    $this->put("/app/invitations/{$invitation->id}/gift", ['accounts' => [
        ['owner' => 'groom', 'bank' => 'ABA', 'account_name' => 'CHAN SOKHA', 'account_number' => '001'],
    ]])->assertSessionHasNoErrors();

    $this->put("/app/invitations/{$invitation->id}/wording", [
        'locale' => 'en',
        'lunar_date' => null,
        'opening' => ['km' => 'សូមអញ្ជើញ', 'en' => null],
        'closing' => ['km' => null, 'en' => null],
        'thank_you' => ['km' => null, 'en' => 'Thanks!'],
    ])->assertSessionHasNoErrors();

    $premium = Theme::factory()->create(['tier' => 'premium']);
    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $premium->id])->assertSessionHasNoErrors();
    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => Theme::factory()->create(['is_active' => false])->id])
        ->assertSessionHasErrors('theme_id');

    $invitation->refresh();
    expect($invitation->giftAccounts()->value('account_number'))->toBe('001')
        ->and($invitation->content)->toMatchArray(['locale' => 'en', 'video_url' => 'https://youtu.be/abcdefghijk'])
        ->and($invitation->content['opening']['km'])->toBe('សូមអញ្ជើញ')
        ->and($invitation->theme_id)->toBe($premium->id);
});

it('previews the saved draft without the envelope intro, with the ribbon until paid', function () {
    $invitation = editable();

    $this->actingAs($invitation->user)->get("/app/invitations/{$invitation->id}/preview")
        ->assertOk()
        ->assertSee('ចាន់ សុខា')
        ->assertSee('is-open', false)
        ->assertSee('has-ribbon', false);

    $invitation->forceFill(['paid_at' => now(), 'live_until' => now()->addMonth()])->save();

    $this->get("/app/invitations/{$invitation->id}/preview")->assertDontSee('has-ribbon', false);
});
