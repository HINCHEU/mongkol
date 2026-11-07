<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\Wish;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Who's coming (with head counts for table planning) and the guest book, which the couple moderates.
class ResponsesController extends Controller
{
    public function show(Invitation $invitation): Response
    {
        $rsvps = $invitation->rsvps()->latest('updated_at')->get();
        $coming = $rsvps->where('attending', true);

        return Inertia::render('Invitations/Responses', [
            'invitation' => $invitation->only('id', 'slug'),
            'settings' => [
                'rsvp_enabled' => $invitation->accepts('rsvp'),
                'wishes_enabled' => $invitation->accepts('wishes'),
                'wishes_need_approval' => (bool) ($invitation->content['wishes_need_approval'] ?? false),
            ],
            'totals' => [
                'replies' => $rsvps->count(),
                'attending' => $coming->count(),
                'people' => (int) $coming->sum('party_size'),
                'declined' => $rsvps->where('attending', false)->count(),
                // Personal links that haven't answered yet.
                'waiting' => $invitation->guests()->whereDoesntHave('rsvp')->count(),
            ],
            'rsvps' => $rsvps->map(fn (Rsvp $r) => [
                ...$r->only('id', 'name', 'attending', 'party_size', 'message'),
                'personal' => $r->guest_id !== null,
                'at' => $r->updated_at->toIso8601String(),
            ]),
            'wishes' => $invitation->wishes()->latest('id')->get()->map(fn (Wish $w) => [
                ...$w->only('id', 'name', 'message', 'is_hidden'),
                'at' => $w->created_at->toIso8601String(),
            ]),
        ]);
    }

    public function settings(Request $request, Invitation $invitation): RedirectResponse
    {
        $data = $request->validate([
            'rsvp_enabled' => ['required', 'boolean'],
            'wishes_enabled' => ['required', 'boolean'],
            'wishes_need_approval' => ['required', 'boolean'],
        ]);

        // A normal save: turning a section off changes the invitation page, so its cached view must go.
        $invitation->update(['content' => [...$invitation->content ?? [], ...array_map('boolval', $data)]]);

        return back();
    }

    public function moderate(Request $request, Invitation $invitation, Wish $wish): RedirectResponse
    {
        $wish->forceFill(['is_hidden' => $request->boolean('hidden')])->save();

        return back();
    }

    public function destroyWish(Invitation $invitation, Wish $wish): RedirectResponse
    {
        $wish->delete();
        $invitation->touch(); // a deleted wish doesn't touch, so clear the cached view here

        return back();
    }

    public function destroyRsvp(Invitation $invitation, Rsvp $rsvp): RedirectResponse
    {
        $rsvp->delete();

        return back();
    }

    /** Replies and wishes, one sheet each. */
    public function export(Invitation $invitation): BinaryFileResponse
    {
        $path = sys_get_temp_dir().'/responses-'.Str::random(20).'.xlsx';
        $writer = SimpleExcelWriter::create($path);

        $writer->nameCurrentSheet('RSVP');
        foreach ($invitation->rsvps()->oldest('id')->lazy() as $r) {
            $writer->addRow([
                'name' => $r->name, 'attending' => $r->attending ? 'yes' : 'no', 'people' => $r->party_size,
                'message' => $r->message, 'personal link' => $r->guest_id ? 'yes' : '', 'replied' => $r->updated_at->toDateTimeString(),
            ]);
        }

        $writer->addNewSheetAndMakeItCurrent('Wishes');
        foreach ($invitation->wishes()->oldest('id')->lazy() as $w) {
            $writer->addRow(['name' => $w->name, 'message' => $w->message, 'hidden' => $w->is_hidden ? 'yes' : '', 'sent' => $w->created_at->toDateTimeString()]);
        }

        $writer->close();

        return response()->download($path, "responses-{$invitation->slug}.xlsx")->deleteFileAfterSend();
    }
}
