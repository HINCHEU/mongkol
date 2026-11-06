<?php

namespace App\Http\Controllers\Public;

use App\Enums\InvitationState;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\PlanLimits;
use App\Services\Themes\InvitationViewModel;
use App\Services\Themes\KhmerFormat;
use App\Services\Themes\ThemeRenderer;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Response;

/**
 * What guests open: `/{slug}` (generic) and `/{slug}/{code}` (personal). Guests never log in, so the
 * link alone decides what they see. These routes are registered last so they can't swallow others.
 */
class InvitationController extends Controller
{
    public function show(ThemeRenderer $renderer, string $slug, ?string $code = null): Response
    {
        // shortcut: two indexed queries per open; the heavy view data is cached already. Cache the
        // slug and guest lookups too if opens ever show up in slow-query logs.
        $invitation = $this->invitation($slug);
        $guest = $code !== null ? $this->guest($invitation, $code) : null;
        $state = $invitation->state();

        if (in_array($state, [InvitationState::Paused, InvitationState::Ended], true)) {
            // Neutral by rule: nothing about the couple is passed, so nothing can leak.
            return $this->noindex(response()->view('public.unavailable', ['state' => $state]));
        }

        $vm = InvitationViewModel::fromInvitation($invitation)->withGuest($guest);

        return $this->noindex(response($renderer->render(PlanLimits::for($invitation)->themeSlug(), $vm, [
            'calendarUrl' => url("{$slug}/calendar.ics"),
            // Only personal links count opens; the generic one is shared in groups.
            'beaconUrl' => $guest ? url("{$slug}/{$guest->code}/opened") : null,
            'og' => $this->og($vm, $guest ? url("{$slug}/{$guest->code}") : url($slug)),
        ])));
    }

    public function calendar(string $slug): Response
    {
        return $this->noindex(response(InvitationViewModel::fromInvitation($this->invitation($slug))->ics(), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="wedding.ics"',
        ]));
    }

    /** The personal link as a QR code, for printed cards. */
    public function qr(string $slug, string $code): Response
    {
        $guest = $this->guest($this->invitation($slug), $code);
        $svg = (new QRCode(new QROptions(['outputBase64' => false, 'addQuietzone' => true])))->render(url("{$slug}/{$guest->code}"));

        return $this->noindex(response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => "inline; filename=\"{$slug}-{$guest->code}.svg\"",
            'Cache-Control' => 'public, max-age=86400',
        ]));
    }

    /**
     * Fired by the envelope tap, not on page load: link-preview bots fetch the page the moment a
     * link is pasted and would otherwise mark every guest as opened.
     */
    public function opened(string $slug, string $code): Response
    {
        $guest = $this->guest($this->invitation($slug), $code);
        // A query update: the guest doesn't touch the invitation, so its cached view survives.
        $guest->increment('open_count', 1, ['last_opened_at' => now(), 'first_opened_at' => $guest->first_opened_at ?? now()]);

        return response()->noContent();
    }

    private function invitation(string $slug): Invitation
    {
        $invitation = Invitation::where('slug', $slug)->with('theme')->first();
        abort_if($invitation === null || $invitation->state() === InvitationState::Suspended, 404);

        return $invitation;
    }

    private function guest(Invitation $invitation, string $code): Guest
    {
        // The exact-case match matters: codes mix cases, and MySQL's default collation ignores case.
        $guest = $invitation->guests()->where('code', $code)->get()->firstWhere('code', $code);
        abort_if($guest === null, 404);

        return $guest;
    }

    /**
     * Link-preview card. Bots don't run JavaScript, hence server-rendered tags. The name stays text
     * (never drawn into the image): GD can't shape Khmer, so subscript consonants would break.
     *
     * @return array{title: string, description: string, image: ?string, url: string}
     */
    private function og(InvitationViewModel $vm, string $url): array
    {
        $who = $vm->guest ? trim(implode(' ', array_filter([$vm->guest['title'], $vm->guest['name'], $vm->guest['suffix']]))) : 'ភ្ញៀវកិត្តិយស';

        return [
            'title' => "សូមគោរពអញ្ជើញ {$who}",
            'description' => $vm->coupleNames('km').' · '.KhmerFormat::date($vm->weddingDate(), 'km'),
            'image' => $vm->data['cover'],
            'url' => $url,
        ];
    }

    private function noindex(Response $response): Response
    {
        return $response->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
