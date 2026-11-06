<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\ParseGuestList;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guests\GuestRequest;
use App\Http\Requests\Guests\ImportGuestsRequest;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\PlanLimits;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// The couple's guest list: add one, paste or import many (previewed first), export, mark as sent.
class GuestController extends Controller
{
    public function index(Request $request, Invitation $invitation): Response
    {
        return Inertia::render('Guests/Index', [
            'invitation' => $invitation->only('id', 'slug'),
            'guests' => $invitation->guests()->latest('id')->get()->map(fn (Guest $g) => [
                ...$g->only('id', 'code', 'title', 'name', 'suffix', 'group', 'phone', 'open_count'),
                'side' => $g->side?->value,
                'sent' => $g->sent_at !== null,
                'last_opened_at' => $g->last_opened_at?->toIso8601String(),
                'url' => url("{$invitation->slug}/{$g->code}"),
            ]),
            'titles' => Guest::TITLES,
            'suffixes' => Guest::SUFFIXES,
            'max' => PlanLimits::for($invitation)->maxGuests(),
            // Rows parsed by preview(), shown once for the couple to check before importing.
            'preview' => $request->session()->get('guest_preview'),
        ]);
    }

    public function store(GuestRequest $request, Invitation $invitation): RedirectResponse
    {
        $invitation->guests()->create($request->validated());

        return back();
    }

    public function update(GuestRequest $request, Invitation $invitation, Guest $guest): RedirectResponse
    {
        $guest->update($request->validated());

        return back();
    }

    public function destroy(Invitation $invitation, Guest $guest): RedirectResponse
    {
        $guest->delete();

        return back();
    }

    /** Pasted names or an uploaded sheet, parsed into rows for the preview; nothing is saved yet. */
    public function preview(Request $request, Invitation $invitation): RedirectResponse
    {
        $request->validate([
            'text' => ['required_without:file', 'nullable', 'string', 'max:200000'],
            'file' => ['required_without:text', 'nullable', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
        ]);

        $rows = $request->hasFile('file')
            ? ParseGuestList::fromFile($request->file('file'))
            : ParseGuestList::fromText($request->input('text'));

        return back()->with($rows === [] ? 'error' : 'guest_preview', $rows === [] ? __('No names found.') : $rows);
    }

    public function import(ImportGuestsRequest $request, Invitation $invitation): RedirectResponse
    {
        DB::transaction(function () use ($request, $invitation) {
            foreach ($request->validated('rows') as $row) {
                $invitation->guests()->create($row);
            }
        });

        return back();
    }

    /** The same columns import reads, plus each guest's link, so a sheet can round-trip. */
    public function export(Invitation $invitation): BinaryFileResponse
    {
        $path = sys_get_temp_dir().'/guests-'.Str::random(20).'.xlsx';
        $writer = SimpleExcelWriter::create($path);

        foreach ($invitation->guests()->oldest('id')->lazy() as $g) {
            $writer->addRow([
                'title' => $g->title, 'name' => $g->name, 'suffix' => $g->suffix, 'side' => $g->side?->value,
                'group' => $g->group, 'phone' => $g->phone, 'link' => url("{$invitation->slug}/{$g->code}"),
                'sent' => $g->sent_at ? 'yes' : '', 'opens' => $g->open_count,
            ]);
        }

        $writer->close();

        return response()->download($path, "guests-{$invitation->slug}.xlsx")->deleteFileAfterSend();
    }

    public function sent(Request $request, Invitation $invitation, Guest $guest): RedirectResponse
    {
        $guest->forceFill(['sent_at' => $request->boolean('sent') ? now() : null])->save();

        return back();
    }
}
