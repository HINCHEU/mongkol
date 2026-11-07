<?php

use App\Actions\CreateInvitation;
use App\Actions\ReviewPayment;
use App\Enums\InvitationState;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use App\Notifications\PaymentReviewed;
use App\Notifications\TrialEndingSoon;
use App\Services\PlanLimits;
use App\Services\Themes\InvitationViewModel;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia;
use Konthaina\Khqr\KHQRGenerator;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
    Storage::fake('local');
    Notification::fake();
    config(['app.admin_emails' => ['boss@example.com']]);
});

function payable(): Invitation
{
    $invitation = app(CreateInvitation::class)->handle(User::factory()->create(), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => '2027-01-16',
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);
    test()->actingAs($invitation->user);

    return $invitation;
}

function plan(string $slug): Plan
{
    return Plan::where('slug', $slug)->first();
}

function boss(): User
{
    return User::firstWhere('email', 'boss@example.com') ?? User::factory()->admin()->create(['email' => 'boss@example.com']);
}

it('gives the trial Premium limits except personal links, and a paid invitation its plan’s', function () {
    $invitation = payable();

    $trial = PlanLimits::for($invitation);
    expect($trial->maxGuests())->toBe(10)
        ->and($trial->galleryPhotos())->toBe(30)
        ->and($trial->allows('video'))->toBeTrue();

    $invitation->forceFill(['paid_at' => now(), 'plan_id' => plan('basic')->id])->save();
    $basic = PlanLimits::for($invitation->fresh());
    expect($basic->maxGuests())->toBe(300)
        ->and($basic->galleryPhotos())->toBe(6)
        ->and($basic->allows('video'))->toBeFalse()
        ->and($basic->allows('premium_themes'))->toBeFalse();

    $invitation->forceFill(['plan_id' => plan('premium')->id])->save();
    expect(PlanLimits::for($invitation->fresh())->maxGuests())->toBe(PlanLimits::GUEST_CEILING);
});

it('enforces the limits in the editor', function () {
    $invitation = payable();
    $invitation->guests()->createMany(array_fill(0, 10, ['name' => 'Guest']));

    $this->post("/app/invitations/{$invitation->id}/guests", ['name' => 'Eleventh'])->assertSessionHasErrors('name');

    $invitation->forceFill(['paid_at' => now(), 'plan_id' => plan('basic')->id])->save();
    $premiumTheme = Theme::factory()->create(['tier' => 'premium']);

    $this->post("/app/invitations/{$invitation->id}/guests", ['name' => 'Eleventh'])->assertSessionHasNoErrors();
    $this->put("/app/invitations/{$invitation->id}/photos", ['video_url' => 'https://youtu.be/abcdefghijk'])->assertSessionHasErrors('video_url');
    $this->put("/app/invitations/{$invitation->id}/theme", ['theme_id' => $premiumTheme->id])->assertSessionHasErrors('theme_id');
    $this->post("/app/invitations/{$invitation->id}/media", ['kind' => 'music', 'file' => UploadedFile::fake()->create('a.mp3', 10, 'audio/mpeg')])
        ->assertSessionHasErrors('file');
});

it('keeps what the plan lacks but doesn’t show it', function () {
    $invitation = payable();
    $invitation->update(['content' => ['video_url' => 'https://youtu.be/abcdefghijk'], 'theme_id' => Theme::factory()->create(['tier' => 'premium', 'slug' => 'test-cover'])->id]);
    foreach (range(1, 8) as $i) {
        $invitation->media()->create(['kind' => 'gallery', 'path' => "{$i}.webp", 'variants' => [960 => "{$i}.webp"], 'sort' => $i]);
    }
    $invitation->media()->create(['kind' => 'music', 'path' => 'song.mp3']);

    $data = InvitationViewModel::fromInvitation($invitation->fresh())->data;
    expect($data['gallery'])->toHaveCount(8)->and($data['video_url'])->not->toBeNull()->and($data['music'])->not->toBeNull();

    $invitation->forceFill(['paid_at' => now(), 'plan_id' => plan('basic')->id])->save();
    $data = InvitationViewModel::fromInvitation($invitation->fresh())->data;
    expect($data['gallery'])->toHaveCount(6)->and($data['video_url'])->toBeNull()->and($data['music'])->toBeNull()
        ->and(PlanLimits::for($invitation->fresh())->themeSlug())->toBe('base');

    expect(PlanLimits::for($invitation->fresh())->beyond(plan('basic')))->toHaveCount(4);
});

it('runs a payment: plan and currency, reference, proof, Telegram alert, approval', function () {
    config(['services.telegram.bot_token' => 'test-token']);
    Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['value' => '42']);
    Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
    $invitation = payable();

    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('premium')->id, 'currency' => 'KHR'])->assertSessionHasNoErrors();
    $payment = $invitation->payments()->sole();
    expect($payment)->reference->toBe("KW-{$payment->id}")->amount_minor->toBe(116000)->currency->toBe('KHR')->status->toBe('pending')
        ->and($payment->amount())->toBe('116,000៛');

    $this->post("/app/invitations/{$invitation->id}/upgrade/{$payment->id}/proof", ['file' => UploadedFile::fake()->image('proof.png')])
        ->assertSessionHasNoErrors();
    $proof = $payment->fresh()->proof;
    expect(Storage::disk('local')->exists($proof->path))->toBeTrue()
        ->and($proof->url())->toBeNull(); // never public

    Http::assertSent(fn ($request) => str_contains($request->url(), 'bottest-token/sendMessage')
        && $request['chat_id'] === '42' && str_contains($request['text'], "KW-{$payment->id}") && str_contains($request['text'], '116,000៛'));

    $this->get('/sokha-dara')->assertSee('has-ribbon', false);
    ReviewPayment::approve($payment->fresh(), boss());

    $invitation->refresh();
    expect($invitation->state())->toBe(InvitationState::Live)
        ->and($invitation->plan_id)->toBe(plan('premium')->id)
        ->and($invitation->live_until->toIso8601String())->toBe('2027-02-15T16:59:59+00:00') // 30 days after, end of day in Phnom Penh
        ->and($payment->fresh()->status)->toBe('approved');
    $this->get('/sokha-dara')->assertDontSee('has-ribbon', false);
    Notification::assertSentTo($invitation->user, PaymentReviewed::class);
});

it('charges the difference from Basic to Premium, then has nothing left to sell', function () {
    $invitation = payable();
    $invitation->forceFill(['paid_at' => now(), 'plan_id' => plan('basic')->id])->save();

    $this->get("/app/invitations/{$invitation->id}/upgrade")
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Invitations/Upgrade', false)
            ->has('plans', 1)->where('plans.0.prices.USD', '$14.00'));

    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD'])->assertSessionHasErrors('plan_id');
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('premium')->id, 'currency' => 'USD']);
    ReviewPayment::approve($invitation->payments()->sole(), boss());

    $this->get("/app/invitations/{$invitation->id}/upgrade")->assertInertia(fn (AssertableInertia $page) => $page->has('plans', 0));
});

it('rejects with a reason the couple sees, and replaces an unreviewed request', function () {
    $invitation = payable();
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD']);
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('premium')->id, 'currency' => 'USD']);
    $payment = $invitation->payments()->sole();
    expect($payment->plan_id)->toBe(plan('premium')->id);

    ReviewPayment::reject($payment, boss(), 'No transfer found');
    Notification::assertSentTo($invitation->user, PaymentReviewed::class);
    expect(fn () => ReviewPayment::approve($payment->fresh(), boss()))->toThrow(InvalidArgumentException::class);

    $this->actingAs($invitation->user)->get("/app/invitations/{$invitation->id}/upgrade")
        ->assertInertia(fn (AssertableInertia $page) => $page->where('rejected.note', 'No transfer found')->where('pending', null));
    $this->post("/app/invitations/{$invitation->id}/upgrade/{$payment->id}/proof", ['file' => UploadedFile::fake()->image('p.png')])->assertNotFound();
});

it('shows the proof to the admin only, through a signed link', function () {
    $invitation = payable();
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD']);
    $payment = $invitation->payments()->sole();
    $this->post("/app/invitations/{$invitation->id}/upgrade/{$payment->id}/proof", ['file' => UploadedFile::fake()->image('p.png')]);

    $signed = URL::temporarySignedRoute('admin.payment-proof', now()->addMinutes(5), $payment);
    $this->get($signed)->assertForbidden(); // the couple
    $this->actingAs(boss())->get($signed)->assertOk();
    $this->get(route('admin.payment-proof', $payment))->assertForbidden(); // unsigned
    $this->delete("/app/invitations/{$invitation->id}/media/{$payment->fresh()->proof_media_id}")->assertNotFound(); // proofs belong to the payment
});

it('approves from the admin Payments list', function () {
    $invitation = payable();
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD']);
    $payment = $invitation->payments()->sole();

    $this->actingAs(boss());
    Livewire::test(ListPayments::class)
        ->assertCanSeeTableRecords([$payment])
        ->callTableAction('reject', $payment, ['reason' => ''])
        ->assertHasTableActionErrors(['reason' => 'required']);

    Livewire::test(ListPayments::class)->callTableAction('approve', $payment);

    expect($payment->fresh()->status)->toBe('approved')->and($invitation->fresh()->state())->toBe(InvitationState::Live);
});

it('emails one “preview ends soon” reminder in the trial’s last 12 hours', function () {
    $soon = payable();
    $soon->forceFill(['trial_ends_at' => now()->addHours(5)])->save();
    $later = payable();

    $this->artisan('invitations:remind-trial')->assertSuccessful();
    $this->artisan('invitations:remind-trial')->assertSuccessful();

    Notification::assertSentToTimes($soon->user, TrialEndingSoon::class, 1);
    Notification::assertNotSentTo($later->user, TrialEndingSoon::class);
});

it('keeps the paused page neutral: no names', function () {
    $invitation = payable();
    $invitation->forceFill(['trial_ends_at' => now()->subMinute()])->save();

    $this->get('/sokha-dara')->assertOk()->assertDontSee('ចាន់ សុខា')->assertDontSee('Sokha');
});

function withBakong(): void
{
    config(['services.bakong' => [
        'token' => 'bakong-token', 'account_id' => 'mongkol@aclb', 'merchant_name' => 'Mongkol',
        'merchant_city' => 'Phnom Penh', 'url' => 'https://api-bakong.nbc.gov.kh',
    ]]);
}

function bakongSays(?array $tx): array
{
    return $tx ? ['responseCode' => 0, 'errorCode' => null, 'data' => $tx] : ['responseCode' => 1, 'errorCode' => 1, 'data' => null];
}

it('pays by Bakong: a dynamic KHQR for the exact amount, approved once Bakong has it', function () {
    withBakong();
    $invitation = payable();
    Http::fake(['api-bakong.nbc.gov.kh/*' => Http::sequence()
        ->push(bakongSays(null))
        ->push(bakongSays(['toAccountId' => 'mongkol@aclb', 'currency' => 'KHR', 'amount' => 116000.0])),
    ]);

    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('premium')->id, 'currency' => 'KHR'])->assertSessionHasNoErrors();
    $payment = $invitation->payments()->sole();
    expect($payment)->method->toBe('bakong')->md5->toBe(md5($payment->khqr))
        ->and(KHQRGenerator::verify($payment->khqr))->toBeTrue()
        ->and($payment->khqr)->toContain($payment->reference)->toContain('mongkol@aclb')
        ->and($payment->expires_at->diffInMinutes(now(), true))->toBeGreaterThan(9);

    $this->get("/app/invitations/{$invitation->id}/upgrade")->assertInertia(fn (AssertableInertia $page) => $page
        ->where('bakong', true)->where('pending.method', 'bakong')
        ->where('pending.khqr', fn ($src) => str_starts_with($src, 'data:image/svg+xml;base64,')));

    $this->artisan('payments:check-bakong')->assertSuccessful(); // not paid yet
    expect($payment->fresh()->status)->toBe('pending');

    $this->artisan('payments:check-bakong')->assertSuccessful();
    expect($payment->fresh())->status->toBe('approved')->reviewed_by->toBeNull()
        ->and($invitation->fresh()->state())->toBe(InvitationState::Live);
    Http::assertSent(fn ($request) => $request->url() === 'https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5'
        && $request->hasHeader('Authorization', 'Bearer bakong-token') && $request['md5'] === $payment->md5);
    Notification::assertSentTo($invitation->user, PaymentReviewed::class);

    $this->artisan('payments:check-bakong'); // nothing left to check
    Http::assertSentCount(2);
});

it('does not approve a Bakong transaction that does not match, and stops checking it', function () {
    withBakong();
    $invitation = payable();
    Http::fake(['api-bakong.nbc.gov.kh/*' => Http::response(bakongSays(['toAccountId' => 'mongkol@aclb', 'currency' => 'KHR', 'amount' => 1000.0]))]);
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('premium')->id, 'currency' => 'KHR']);

    $this->artisan('payments:check-bakong');
    $this->artisan('payments:check-bakong');

    expect($invitation->payments()->sole())->status->toBe('pending')->note->toContain('does not match')
        ->and($invitation->fresh()->paid_at)->toBeNull();
    Http::assertSentCount(1);
});

it('keeps manual payment as the fallback, and checks a Bakong QR before replacing it', function () {
    $invitation = payable();
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD']);
    expect($invitation->payments()->sole())->method->toBe('manual_khqr')->khqr->toBeNull(); // no token: manual only

    withBakong();
    Http::fake(['api-bakong.nbc.gov.kh/*' => Http::sequence()
        ->push(bakongSays(null))
        ->push(bakongSays(['toAccountId' => 'mongkol@aclb', 'currency' => 'USD', 'amount' => 15.0])),
    ]);
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD']);
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD', 'method' => 'manual_khqr']);
    expect($invitation->payments()->sole())->method->toBe('manual_khqr'); // unpaid QR checked, then replaced

    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD']);
    $qr = $invitation->payments()->where('method', 'bakong')->sole();
    // The couple paid this QR, then tapped "pay manually": it's approved, not thrown away.
    $this->post("/app/invitations/{$invitation->id}/upgrade", ['plan_id' => plan('basic')->id, 'currency' => 'USD', 'method' => 'manual_khqr']);
    expect($qr->fresh()->status)->toBe('approved')->and($invitation->payments()->count())->toBe(1);
});
