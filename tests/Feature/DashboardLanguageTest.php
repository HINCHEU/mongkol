<?php

use App\Actions\CreateInvitation;
use App\Http\Controllers\Dashboard\EditorController;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Arr;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
});

it('has every dashboard key in both languages', function () {
    $keys = fn (string $locale) => array_keys(Arr::dot(require lang_path("{$locale}/dashboard.php")));

    expect(array_diff($keys('en'), $keys('km')))->toBe([])
        ->and(array_diff($keys('km'), $keys('en')))->toBe([]);
});

it('uses every key the Vue pages ask for', function () {
    $known = Arr::dot(require lang_path('en/dashboard.php'));
    $files = collect(glob(resource_path('js/dashboard/{,*/,*/*/}*.vue'), GLOB_BRACE));

    // Literal keys only; keys built from a value (`states.${state}`) are covered by the group checks below.
    $used = $files->flatMap(fn ($file) => preg_match_all("/\\bt\\('([a-z_.]+)'/", file_get_contents($file), $m) ? $m[1] : [])->unique();

    expect($files->count())->toBeGreaterThan(15)
        ->and($used->count())->toBeGreaterThan(100)
        ->and($used->reject(fn ($key) => isset($known[$key]))->values()->all())->toBe([])
        ->and(array_keys(trans('dashboard.states', [], 'en')))->toBe(['trial', 'live', 'paused', 'ended', 'suspended'])
        ->and(array_keys(trans('dashboard.editor.steps', [], 'en')))->toEqualCanonicalizing(EditorController::STEPS);
});

it('shares the copy in the user’s language', function () {
    $user = User::factory()->create(['locale' => 'km']);

    $this->actingAs($user)->get('/app')->assertInertia(fn (AssertableInertia $page) => $page
        ->where('locale', 'km')
        ->where('translations.home.title', 'សំបុត្ររបស់ខ្ញុំ'));

    $user->update(['locale' => 'en']);
    $this->get('/app')->assertInertia(fn (AssertableInertia $page) => $page->where('translations.home.title', 'My invitations'));
});

it('explains validation errors in Khmer, with Khmer field names', function () {
    $invitation = app(CreateInvitation::class)->handle(User::factory()->create(['locale' => 'km']), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => now()->addMonths(2)->toDateString(),
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);

    $this->actingAs($invitation->user)->put("/app/invitations/{$invitation->id}/venues", ['venues' => [['name' => '']]])
        ->assertSessionHasErrors(['venues.0.name' => 'សូមបំពេញ ឈ្មោះទីតាំង។']);

    $invitation->user->update(['locale' => 'en']);
    $this->put("/app/invitations/{$invitation->id}/venues", ['venues' => [['name' => '']]])
        ->assertSessionHasErrors(['venues.0.name' => 'The venue name field is required.']);
});
