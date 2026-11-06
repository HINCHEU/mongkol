<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\SyncChildren;
use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\UpdateCoupleRequest;
use App\Http\Requests\Editor\UpdateGiftRequest;
use App\Http\Requests\Editor\UpdateProgramRequest;
use App\Http\Requests\Editor\UpdateThemeRequest;
use App\Http\Requests\Editor\UpdateVenuesRequest;
use App\Http\Requests\Editor\UpdateWordingRequest;
use App\Models\EventPreset;
use App\Models\Invitation;
use App\Models\InvitationPerson;
use App\Models\Theme;
use App\Services\LunarDate;
use App\Services\Themes\InvitationViewModel;
use App\Services\Themes\KhmerFormat;
use App\Services\Themes\ThemeRenderer;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

// The couple's editor: one page per step, each step autosaving through its own PUT and Form Request.
class EditorController extends Controller
{
    // Venues come before the program, so ceremonies can be placed at them.
    public const STEPS = ['couple', 'venues', 'program', 'gift', 'wording', 'theme'];

    public function edit(Invitation $invitation, string $step = 'couple'): Response
    {
        $invitation->load(['people', 'venues', 'events', 'giftAccounts']);
        $people = $invitation->people->keyBy('role');

        return Inertia::render('Invitations/Edit', [
            'step' => $step,
            'steps' => self::STEPS,
            'invitation' => [
                'id' => $invitation->id,
                'slug' => $invitation->slug,
                'state' => $invitation->state()->value,
                'theme_id' => $invitation->theme_id,
                'wedding_date' => $invitation->wedding_date->toDateString(),
                'people' => collect(InvitationPerson::ROLES)->mapWithKeys(fn ($role) => [$role => [
                    'title' => $people[$role]->title ?? null,
                    'name_km' => $people[$role]->name_km ?? null,
                    'name_en' => $people[$role]->name_en ?? null,
                    'is_late' => (bool) ($people[$role]->is_late ?? false),
                ]]),
                'venues' => $invitation->venues->map->only('id', 'name', 'address', 'maps_url')->values(),
                'events' => $invitation->events->map(fn ($e) => [
                    ...$e->only('id', 'preset_key', 'title_km', 'title_en', 'venue_id', 'note'),
                    'date' => $e->starts_at->setTimezone(KhmerFormat::TIMEZONE)->toDateString(),
                    'time' => $e->starts_at->setTimezone(KhmerFormat::TIMEZONE)->format('H:i'),
                ])->values(),
                'gift_accounts' => $invitation->giftAccounts->map->only('id', 'owner', 'bank', 'account_name', 'account_number')->values(),
                'content' => [
                    'locale' => $invitation->content['locale'] ?? 'km',
                    'lunar_date' => $invitation->content['lunar_date'] ?? null,
                    ...collect(['opening', 'closing', 'thank_you'])->mapWithKeys(fn ($key) => [$key => [
                        'km' => $invitation->content[$key]['km'] ?? null,
                        'en' => $invitation->content[$key]['en'] ?? null,
                    ]]),
                ],
            ],
            'lunarDate' => LunarDate::for($invitation->wedding_date),
            'presets' => EventPreset::where('is_active', true)->orderBy('default_sort')->get(['key', 'title_km', 'title_en']),
            'themes' => Theme::where('is_active', true)->orderBy('sort')->get(['id', 'slug', 'name', 'tier']),
            'previewUrl' => route('invitations.preview', $invitation),
            'publicUrl' => url($invitation->slug),
        ]);
    }

    public function couple(UpdateCoupleRequest $request, Invitation $invitation): RedirectResponse
    {
        DB::transaction(function () use ($request, $invitation) {
            foreach ($request->validated('people') as $role => $person) {
                if (blank($person['name_km'] ?? null)) {
                    // An emptied parent is removed; the couple can't be, validation requires both.
                    $invitation->people()->where('role', $role)->first()?->delete();

                    continue;
                }

                $invitation->people()->updateOrCreate(['role' => $role], ['is_late' => false, ...$person]);
            }
        });

        return back();
    }

    public function venues(UpdateVenuesRequest $request, Invitation $invitation): RedirectResponse
    {
        DB::transaction(fn () => SyncChildren::handle($invitation->venues(), $request->validated('venues')));

        return back();
    }

    public function program(UpdateProgramRequest $request, Invitation $invitation): RedirectResponse
    {
        $events = collect($request->validated('events'))->map(fn (array $e) => [
            'id' => $e['id'] ?? null,
            'preset_key' => $e['preset_key'] ?? null,
            'title_km' => $e['title_km'],
            'title_en' => $e['title_en'] ?? null,
            'venue_id' => $e['venue_id'] ?? null,
            'note' => $e['note'] ?? null,
            'starts_at' => CarbonImmutable::parse("{$e['date']} {$e['time']}", KhmerFormat::TIMEZONE)->utc(),
        ])->all();

        DB::transaction(function () use ($request, $invitation, $events) {
            $invitation->update(['wedding_date' => $request->validated('wedding_date')]);
            SyncChildren::handle($invitation->events(), $events, sorted: true);
        });

        return back();
    }

    public function gift(UpdateGiftRequest $request, Invitation $invitation): RedirectResponse
    {
        DB::transaction(fn () => SyncChildren::handle($invitation->giftAccounts(), $request->validated('accounts')));

        return back();
    }

    public function wording(UpdateWordingRequest $request, Invitation $invitation): RedirectResponse
    {
        // Merged, so content keys other steps own (e.g. video_url) are kept.
        $invitation->update(['content' => [...$invitation->content ?? [], ...$request->validated()]]);

        return back();
    }

    public function theme(UpdateThemeRequest $request, Invitation $invitation): RedirectResponse
    {
        // Content is theme-agnostic, so switching keeps everything the couple entered.
        $invitation->update($request->validated());

        return back();
    }

    /** Draft data in any state, with the preview ribbon until paid. Shown in the editor's phone frame. */
    public function preview(Invitation $invitation, ThemeRenderer $renderer): View
    {
        $data = InvitationViewModel::fromInvitation($invitation)->data;

        return $renderer->render($invitation->theme->slug, new InvitationViewModel($data, isPreview: $invitation->paid_at === null), [
            'skipIntro' => true,
        ]);
    }
}
