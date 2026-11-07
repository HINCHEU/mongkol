<?php

use App\Actions\CreateInvitation;
use App\Models\Invitation;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
});

function launched(): Invitation
{
    $invitation = app(CreateInvitation::class)->handle(User::factory()->create(), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => now()->addMonths(2)->toDateString(),
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);
    $invitation->guests()->make(['name' => 'ចាន់ ថា'])->forceFill(['code' => 'k7Px2Q'])->save();

    return $invitation;
}

it('sends security headers and a content policy on guest, dashboard and admin pages', function () {
    $invitation = launched();
    config(['app.admin_emails' => ['admin@example.com']]);

    $responses = [
        $this->get('/sokha-dara/k7Px2Q'),
        $this->actingAs($invitation->user)->get('/app'),
        $this->actingAs(User::factory()->admin()->create(['email' => 'admin@example.com']))->get('/admin'),
    ];

    foreach ($responses as $response) {
        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        expect($response->headers->get('Content-Security-Policy'))
            ->toContain("frame-ancestors 'self'")
            ->toContain("frame-src 'self'") // the editor frames its own preview
            ->toContain("object-src 'none'")
            ->toContain('https://fonts.gstatic.com');
    }
});

it('rate-limits guest pages per IP, without starving the open beacon', function () {
    launched();

    foreach (range(1, 120) as $i) {
        $this->get('/sokha-dara')->assertOk();
    }
    $this->get('/sokha-dara/k7Px2Q')->assertTooManyRequests();

    // Its own counter: page views don't use up the beacon's 30 a minute.
    $this->post('/sokha-dara/k7Px2Q/opened')->assertNoContent();
});

it('refuses a photo that would decode to more than 50 megapixels', function () {
    Storage::fake('local');
    Storage::fake('public');
    $invitation = launched();

    // A valid PNG header claiming 8000×7000 (56 MP): tiny on disk, huge once decoded.
    $ihdr = pack('N2', 8000, 7000)."\x08\x06\x00\x00\x00";
    $png = "\x89PNG\r\n\x1a\n".pack('N', 13).'IHDR'.$ihdr.pack('N', crc32('IHDR'.$ihdr));
    $file = UploadedFile::fake()->createWithContent('huge.png', $png);

    $this->actingAs($invitation->user)->post("/app/invitations/{$invitation->id}/media", ['kind' => 'cover', 'file' => $file])
        ->assertSessionHasErrors('file');
    expect($invitation->media()->count())->toBe(0);
});

it('alerts Telegram once about a production error, not on every repeat', function () {
    config(['services.telegram.bot_token' => 'token']);
    Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['value' => '42']);
    Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
    app()->detectEnvironment(fn () => 'production');

    report(new RuntimeException('Database exploded'));
    report(new RuntimeException('Database exploded'));
    report(new RuntimeException('Something else'));

    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => str_contains($request['text'], 'RuntimeException: Database exploded'));
});

it('stays quiet outside production and when Telegram itself is down', function () {
    config(['services.telegram.bot_token' => 'token', 'services.telegram.chat_id' => '42']);
    Http::fake(['api.telegram.org/*' => Http::response('down', 502)]);

    report(new RuntimeException('local only'));
    Http::assertNothingSent();

    app()->detectEnvironment(fn () => 'production');
    report(new RuntimeException('telegram is down'));
    Http::assertSentCount(1); // tried once, failed, and the failure didn't loop back into another alert

    $this->artisan('telegram:alert', ['message' => 'Backup failed'])->assertSuccessful();
    Http::assertSentCount(2);
});
