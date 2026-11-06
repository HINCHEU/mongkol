<?php

use App\Models\Invitation;
use App\Models\Theme;
use App\Services\Themes\InvitationViewModel;
use App\Services\Themes\ThemeRegistry;
use Database\Seeders\DemoSeeder;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

// Adds tests/Fixtures/themes (a child that overrides only `cover`) next to the real themes.
function withFixtureThemes(): ThemeRegistry
{
    $registry = new ThemeRegistry([resource_path('themes'), base_path('tests/Fixtures/themes')]);
    app()->instance(ThemeRegistry::class, $registry);
    $registry->registerViews();

    return $registry;
}

beforeEach(fn () => $this->withoutVite());

it('reads manifests and resolves extends', function () {
    $registry = withFixtureThemes();

    expect($registry->get('base')['chain'])->toBe(['base'])
        ->and($registry->get('test-cover')['chain'])->toBe(['test-cover', 'base'])
        ->and($registry->get('test-cover')['sections'])->toBe($registry->get('base')['sections'])
        ->and($registry->get('test-cover')['name'])->toBe('Test Cover')
        ->and($registry->entry('test-cover'))->toBe('resources/themes/base/assets/app.js');
});

it('refuses a theme that extends a missing one', function () {
    $dir = storage_path('framework/testing/themes-broken');
    File::ensureDirectoryExists("{$dir}/orphan");
    File::put("{$dir}/orphan/theme.json", '{"slug":"orphan","name":"Orphan","version":"1.0.0","extends":"nope"}');

    try {
        expect(fn () => (new ThemeRegistry([$dir]))->all())->toThrow(RuntimeException::class, 'missing or circular');
    } finally {
        File::deleteDirectory($dir);
    }
});

it('syncs themes into the table, base active and new ones inactive', function () {
    withFixtureThemes();

    $this->artisan('themes:sync')->assertSuccessful();

    expect(Theme::where('slug', 'base')->value('is_active'))->toBeTrue()
        ->and(Theme::where('slug', 'test-cover')->value('is_active'))->toBeFalse();

    // A second sync keeps the admin's choices.
    Theme::where('slug', 'test-cover')->update(['is_active' => true, 'tier' => 'premium']);
    $this->artisan('themes:sync')->assertSuccessful();

    expect(Theme::where('slug', 'test-cover')->first())->is_active->toBeTrue()->tier->toBe('premium');
});

it('renders the base demo with envelope, lunar date and map', function () {
    $this->artisan('themes:sync');

    $this->get('/themes/base')
        ->assertOk()
        ->assertSee('class="envelope"', false)
        ->assertSee('លោក ចាន់ ថា និងភរិយា')
        ->assertSee('ចាន់ សុខា')
        ->assertSee('ថ្ងៃសៅរ៍ ៣ កើត ខែមិគសិរ ឆ្នាំមមី អដ្ឋស័ក ពុទ្ធសករាជ ២៥៧០')
        ->assertSee('ថ្ងៃសៅរ៍ ទី១២ ខែធ្នូ ឆ្នាំ២០២៦')
        ->assertSee('ម៉ោង ៧:០០ ព្រឹក')
        ->assertSee('https://maps.google.com/?q=11.5560,104.9282', false)
        ->assertSee('noindex, nofollow', false)
        // No photos in the demo, so the gallery is skipped, not shown empty.
        ->assertDontSee('id="gallery"', false);
});

it('renders a child theme that overrides only the cover', function () {
    withFixtureThemes();
    $this->artisan('themes:sync');
    Theme::where('slug', 'test-cover')->update(['is_active' => true]);

    $this->get('/themes/test-cover')
        ->assertOk()
        ->assertSee('data-test="child-cover"', false)
        ->assertDontSee('class="arch"', false)
        // Every other section still comes from base.
        ->assertSee('id="program"', false)
        ->assertSee('ពិធីចងដៃ');
});

it('hides inactive and unknown themes', function () {
    withFixtureThemes();
    $this->artisan('themes:sync');

    $this->get('/themes/test-cover')->assertNotFound();
    $this->get('/themes/no-such-theme')->assertNotFound();
    $this->get('/themes')->assertOk()->assertSee('Morodok Snaeha')->assertDontSee('Test Cover');
});

it('serves the demo calendar file', function () {
    $this->artisan('themes:sync');

    $response = $this->get('/themes/base/calendar.ics')->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('text/calendar')
        ->and($response->getContent())
        ->toContain('BEGIN:VCALENDAR')
        ->toContain('DTSTART:20261212T000000Z')
        ->toContain('SUMMARY:ពិធីហែជំនូន · ចាន់ សុខា & លី ដារ៉ា');
});

it('builds the view from an invitation and clears the cache when it changes', function () {
    $this->seed([ReferenceDataSeeder::class, DemoSeeder::class]);
    $invitation = Invitation::where('slug', 'sokha-dara')->sole();

    $vm = InvitationViewModel::fromInvitation($invitation);

    expect($vm->person('groom')['name_km'])->toBe('ចាន់ សុខា')
        ->and($vm->initials())->toBe('S & D')
        ->and($vm->has('program'))->toBeTrue()
        ->and($vm->has('gallery'))->toBeFalse()
        ->and($vm->data['lunar_date'])->toContain('ខែមិគសិរ')
        ->and($vm->events()[0]['venue_name'])->toBe('គេហដ្ឋានខាងស្រី')
        ->and($vm->isPreview)->toBeFalse()
        ->and(Cache::has(InvitationViewModel::cacheKey($invitation->id)))->toBeTrue();

    // Editing a child row touches the invitation, which clears its cached view.
    $invitation->people()->where('role', 'groom')->first()->update(['name_km' => 'ចាន់ សុខុម']);

    expect(Cache::has(InvitationViewModel::cacheKey($invitation->id)))->toBeFalse()
        ->and(InvitationViewModel::fromInvitation($invitation->fresh())->person('groom')['name_km'])->toBe('ចាន់ សុខុម');
});

it('adds the guest per request without caching it', function () {
    $this->seed([ReferenceDataSeeder::class, DemoSeeder::class]);
    $invitation = Invitation::where('slug', 'sokha-dara')->sole();

    $vm = InvitationViewModel::fromInvitation($invitation)->withGuest($invitation->guests()->first());

    expect($vm->guest['name'])->not->toBeEmpty()
        ->and(Cache::get(InvitationViewModel::cacheKey($invitation->id)))->not->toHaveKey('guest');
});

it('creates a child theme with make:theme', function () {
    $path = resource_path('themes/zz-test-theme');

    try {
        $this->artisan('make:theme', ['slug' => 'zz-test-theme'])->assertSuccessful();

        expect(json_decode(File::get("{$path}/theme.json"), true))->toMatchArray(['slug' => 'zz-test-theme', 'extends' => 'base'])
            ->and(File::get("{$path}/assets/app.js"))->toContain('base/assets/invitation.js');

        $this->artisan('make:theme', ['slug' => 'zz-test-theme'])->assertFailed();
        $this->artisan('make:theme', ['slug' => 'Bad Slug'])->assertFailed();
    } finally {
        File::deleteDirectory($path);
    }
});
