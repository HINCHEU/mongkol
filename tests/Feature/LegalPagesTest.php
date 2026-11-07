<?php

use App\Actions\CreateInvitation;
use App\Models\Plan;
use App\Models\Setting;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    $this->withoutVite();
});

it('shows privacy, terms and refunds in Khmer by default and in English on request', function () {
    foreach (['privacy' => 'គោលការណ៍ឯកជនភាព', 'terms' => 'លក្ខខណ្ឌប្រើប្រាស់', 'refunds' => 'គោលការណ៍សងប្រាក់វិញ'] as $page => $khmerTitle) {
        $this->get("/{$page}")->assertOk()->assertSee('<html lang="km">', false)->assertSee($khmerTitle);
        $this->get("/{$page}?lang=en")->assertOk()->assertSee('<html lang="en">', false)->assertSee(config('app.contact_email'));
    }
});

it('quotes the trial and live period from the settings, not a fixed number', function () {
    Setting::updateOrCreate(['key' => 'trial_days'], ['value' => 5]);
    Plan::query()->update(['live_days_after_wedding' => 45]);
    config(['app.operator' => 'Hin Cheu, trading as Mongkol']);

    $this->get('/terms?lang=en')
        ->assertSee('a free preview for 5 days')
        ->assertSee('live until 45 days after the wedding date')
        ->assertSee('Hin Cheu, trading as Mongkol')
        ->assertDontSee(':trial_days')
        ->assertDontSee(':operator');
});

it('has the same sections in both languages', function () {
    $shape = fn (string $locale) => array_keys(Arr::dot(require lang_path("{$locale}/legal.php")));

    expect($shape('km'))->toBe($shape('en'));
});

it('keeps the page names out of invitation links', function () {
    expect(app(CreateInvitation::class)->slugFor('', 'Privacy'))->toBe('privacy-2')
        ->and(app(CreateInvitation::class)->slugFor('', 'Terms'))->toBe('terms-2');
});

it('links the pages from sign-in, the landing page and every invitation', function () {
    $this->get('/')->assertSee(route('legal', 'privacy'), false)->assertSee(route('legal', 'refunds'), false);
    $this->get('/themes/base')->assertSee(route('legal', 'privacy'), false);
    expect(trans('dashboard.login.terms', [], 'km'))->toBe('លក្ខខណ្ឌប្រើប្រាស់');
});
