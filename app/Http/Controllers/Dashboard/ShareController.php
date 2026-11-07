<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\MediaKind;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

// Links to send: the generic one on top, then each guest's with copy, share, Telegram, QR and a Sent tick.
class ShareController extends Controller
{
    // {name} is the guest's title, name and suffix (or ភ្ញៀវកិត្តិយស on the generic link); {link} the URL.
    public const DEFAULT_MESSAGE = "សូមគោរពអញ្ជើញ {name} ចូលរួមពិធីមង្គលការរបស់យើងខ្ញុំ។\n{link}";

    public function show(Invitation $invitation): Response
    {
        return Inertia::render('Invitations/Share', [
            'invitation' => [
                ...$invitation->only('id', 'slug'),
                'url' => url($invitation->slug),
                'state' => $invitation->state()->value,
            ],
            // Telegram caches the first preview it sees, so links unlock once there's a cover photo.
            'hasCover' => $invitation->media()->where('kind', MediaKind::Cover)->whereNotNull('variants')->exists(),
            'message' => $invitation->content['share_message'] ?? self::DEFAULT_MESSAGE,
            'guests' => $invitation->guests()->oldest('id')->get()->map(fn (Guest $g) => [
                'id' => $g->id,
                'name' => $g->displayName(),
                'url' => url("{$invitation->slug}/{$g->code}"),
                'qr' => url("{$invitation->slug}/{$g->code}/qr.svg"),
                'sent' => $g->sent_at !== null,
                'open_count' => $g->open_count,
                // "3 hours ago" / "3 ម៉ោងមុន": Carbon follows the dashboard language.
                'last_opened' => $g->last_opened_at?->diffForHumans(),
            ]),
        ]);
    }

    public function update(Request $request, Invitation $invitation): RedirectResponse
    {
        $request->validate(['message' => ['required', 'string', 'max:1000']]);

        // saveQuietly: the message isn't on the invitation page, so the cached view stays valid.
        $invitation->forceFill(['content' => [...$invitation->content ?? [], 'share_message' => $request->input('message')]])->saveQuietly();

        return back();
    }
}
