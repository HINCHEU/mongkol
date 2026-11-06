<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\MediaKind;
use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreMediaRequest;
use App\Jobs\ProcessMedia;
use App\Models\Invitation;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;

// Uploads save at once (not autosaved): cover, gallery photos, music and gift-account KHQR images.
class MediaController extends Controller
{
    public function store(StoreMediaRequest $request, Invitation $invitation): RedirectResponse
    {
        $kind = MediaKind::from($request->validated('kind'));
        $file = $request->file('file');
        $account = $kind === MediaKind::Khqr ? $invitation->giftAccounts()->findOrFail($request->validated('gift_account_id')) : null;

        // One cover, one song, one KHQR per account: a new upload replaces the old one.
        match ($kind) {
            MediaKind::Cover, MediaKind::Music => $invitation->media()->where('kind', $kind)->get()->each->delete(),
            MediaKind::Khqr => $account->khqr?->delete(),
            default => null,
        };

        $media = $invitation->media()->create([
            'kind' => $kind,
            // Music needs no processing, so it goes straight to the public disk; images wait on the private one.
            'path' => $kind === MediaKind::Music
                ? $file->storePublicly("invitations/{$invitation->id}", ['disk' => config('filesystems.media_disk')])
                : $file->store('uploads', 'local'),
            'sort' => $kind === MediaKind::Gallery ? $invitation->media()->where('kind', $kind)->max('sort') + 1 : 0,
        ]);

        $account?->update(['khqr_media_id' => $media->id]);

        if ($kind !== MediaKind::Music) {
            // shortcut: resized in the request (~1-2 s) so the editor shows the photo straight away;
            // with a queue worker, dispatch() instead and have the editor poll photos still processing.
            ProcessMedia::dispatchSync($media);
        }

        return back();
    }

    public function destroy(Invitation $invitation, Media $media): RedirectResponse
    {
        abort_if($media->kind === MediaKind::Proof, 404); // payment proofs belong to the payment
        $media->delete();
        $invitation->touch(); // the KHQR's account isn't touched by the nulled column, so clear the cached view here

        return back();
    }
}
