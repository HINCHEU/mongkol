<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\CreateInvitation;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Home', [
            'invitations' => $request->user()->invitations()->with('people')->latest()->get()
                ->map(fn (Invitation $i) => [
                    'id' => $i->id,
                    'slug' => $i->slug,
                    'wedding_date' => $i->wedding_date->toDateString(),
                    'state' => $i->state()->value,
                    'names' => $i->people->whereIn('role', ['groom', 'bride'])->sortBy('role', descending: true)->pluck('name_km')->implode(' & '),
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Invitations/Create', [
            'themes' => Theme::where('is_active', true)->orderBy('sort')->get(['id', 'slug', 'name', 'tier']),
        ]);
    }

    public function store(StoreInvitationRequest $request, CreateInvitation $create): RedirectResponse
    {
        $invitation = $create->handle($request->user(), $request->validated());

        return redirect()->route('invitations.edit', $invitation);
    }
}
