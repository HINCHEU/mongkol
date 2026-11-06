<?php

use App\Actions\CreateInvitation;
use App\Http\Requests\Guests\GuestRequest;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia;
use Spatie\SimpleExcel\SimpleExcelReader;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
});

function guestList(): Invitation
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

it('adds a guest with a 6-character code from the safe alphabet', function () {
    $invitation = guestList();

    $this->post("/app/invitations/{$invitation->id}/guests", ['title' => 'លោក', 'name' => 'ចាន់ ថា', 'suffix' => 'និងភរិយា', 'side' => 'groom'])
        ->assertSessionHasNoErrors();

    $guest = $invitation->guests()->sole();
    expect($guest->code)->toMatch('/^['.Guest::CODE_ALPHABET.']{6}$/')
        ->and($guest->displayName())->toBe('លោក ចាន់ ថា និងភរិយា');

    $this->post("/app/invitations/{$invitation->id}/guests", ['name' => '', 'side' => 'aunt'])->assertSessionHasErrors(['name', 'side']);
});

it('turns 300 pasted names into 300 guests with their own links', function () {
    $invitation = guestList();
    $lines = collect(range(1, 300))->map(fn ($i) => match ($i % 3) {
        0 => "លោក ភ្ញៀវ {$i} និងភរិយា",
        1 => "លោកស្រី ភ្ញៀវ {$i}",
        default => "ភ្ញៀវ {$i}",
    })->implode("\n");

    $this->post("/app/invitations/{$invitation->id}/guests/preview", ['text' => $lines."\n\n   \n"])->assertSessionHasNoErrors();
    $rows = session('guest_preview');

    expect($rows)->toHaveCount(300)
        ->and($rows[0])->toMatchArray(['title' => 'លោកស្រី', 'name' => 'ភ្ញៀវ 1', 'suffix' => null])
        ->and($rows[2])->toMatchArray(['title' => 'លោក', 'name' => 'ភ្ញៀវ 3', 'suffix' => 'និងភរិយា'])
        ->and($invitation->guests()->count())->toBe(0); // preview saves nothing

    $this->post("/app/invitations/{$invitation->id}/guests/import", ['rows' => $rows])->assertSessionHasNoErrors();

    expect($invitation->guests()->count())->toBe(300)
        ->and($invitation->guests()->distinct()->count('code'))->toBe(300);
});

it('round-trips the Excel export through the import', function () {
    $invitation = guestList();
    $invitation->guests()->create(['title' => 'កញ្ញា', 'name' => 'ហេង លីដា', 'side' => 'bride', 'group' => 'Work', 'phone' => '012345678']);

    $response = $this->get("/app/invitations/{$invitation->id}/guests/export")->assertOk();
    $rows = SimpleExcelReader::create($response->baseResponse->getFile()->getPathname(), 'xlsx')->getRows()->all();
    expect($rows[0])->toMatchArray(['title' => 'កញ្ញា', 'name' => 'ហេង លីដា', 'side' => 'bride', 'link' => url("{$invitation->slug}/".$invitation->guests()->value('code'))]);

    $file = new UploadedFile($response->baseResponse->getFile()->getPathname(), 'guests.xlsx', test: true);
    $this->post("/app/invitations/{$invitation->id}/guests/preview", ['file' => $file])->assertSessionHasNoErrors();
    expect(session('guest_preview'))->toBe([['title' => 'កញ្ញា', 'name' => 'ហេង លីដា', 'suffix' => null, 'side' => 'bride', 'group' => 'Work', 'phone' => '012345678']]);
});

it('reads a CSV with Khmer headers, or a plain list without headers', function () {
    $invitation = guestList();
    $csv = fn (string $body) => UploadedFile::fake()->createWithContent('list.csv', $body);

    $this->post("/app/invitations/{$invitation->id}/guests/preview", ['file' => $csv("ឈ្មោះ,ខាង\nលោក សុខ ជា,ខាងប្រុស\n")]);
    expect(session('guest_preview'))->toBe([['title' => 'លោក', 'name' => 'សុខ ជា', 'suffix' => null, 'side' => 'groom', 'group' => null, 'phone' => null]]);

    $this->post("/app/invitations/{$invitation->id}/guests/preview", ['file' => $csv("សុខ ជា\nហេង លីដា\n")]);
    expect(collect(session('guest_preview'))->pluck('name')->all())->toBe(['សុខ ជា', 'ហេង លីដា']);

    $this->post("/app/invitations/{$invitation->id}/guests/preview", ['text' => "\n  \n"])->assertSessionHasErrors('text');
    $this->post("/app/invitations/{$invitation->id}/guests/preview", ['file' => $csv("ឈ្មោះ\n\n")])->assertSessionHas('error');
});

it('lists, edits, ticks and deletes only the couple’s own guests', function () {
    $invitation = guestList();
    $guest = $invitation->guests()->create(['name' => 'ចាន់ ថា']);
    $other = guestList()->guests()->create(['name' => 'Not yours']);
    $this->actingAs($invitation->user);

    $this->get("/app/invitations/{$invitation->id}/guests")
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Guests/Index', false)->has('guests', 1)
            ->where('guests.0.url', url("{$invitation->slug}/{$guest->code}")));

    $this->put("/app/invitations/{$invitation->id}/guests/{$guest->id}", ['name' => 'ចាន់ ថាវី', 'group' => 'Family'])->assertSessionHasNoErrors();
    $this->put("/app/invitations/{$invitation->id}/guests/{$guest->id}/sent", ['sent' => true]);
    expect($guest->fresh())->name->toBe('ចាន់ ថាវី')->group->toBe('Family')->sent_at->not->toBeNull();

    $this->put("/app/invitations/{$invitation->id}/guests/{$other->id}", ['name' => 'Mine'])->assertNotFound();
    $this->delete("/app/invitations/{$other->invitation_id}/guests/{$other->id}")->assertForbidden();

    $this->delete("/app/invitations/{$invitation->id}/guests/{$guest->id}");
    expect(Guest::find($guest->id))->toBeNull()->and($other->fresh())->not->toBeNull();
});

it('stops at the guest ceiling', function () {
    $invitation = guestList();
    Guest::insert(collect(range(1, GuestRequest::MAX_GUESTS))->map(fn ($i) => [
        'invitation_id' => $invitation->id, 'code' => str_pad((string) $i, 6, 'A'), 'name' => "g{$i}",
    ])->all());

    $this->post("/app/invitations/{$invitation->id}/guests", ['name' => 'One more'])->assertSessionHasErrors('name');
    $this->post("/app/invitations/{$invitation->id}/guests/import", ['rows' => [['name' => 'One more']]])->assertSessionHasErrors('rows');
});
