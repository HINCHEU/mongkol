<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\MediaKind;
use App\Http\Controllers\Controller;
use App\Jobs\SendTelegramAlert;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Services\PlanLimits;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Manual payment, phase 1: pick a plan and currency → pay your KHQR with the KW-{id} reference in
 * the note → upload the screenshot → you get a Telegram alert and approve or reject in admin.
 */
class UpgradeController extends Controller
{
    public const CURRENCIES = ['USD', 'KHR'];

    public function show(Invitation $invitation): Response
    {
        $limits = PlanLimits::for($invitation);
        $pending = $invitation->payments()->where('status', Payment::PENDING)->with('plan')->latest('id')->first();
        $rejected = $invitation->payments()->where('status', Payment::REJECTED)->latest('id')->first();

        return Inertia::render('Invitations/Upgrade', [
            'invitation' => [
                ...$invitation->only('id', 'slug'),
                'state' => $invitation->state()->value,
                'plan' => $invitation->paid_at ? $invitation->plan?->name : null,
                'live_until' => $invitation->live_until?->toIso8601String(),
            ],
            'plans' => $this->upgradable($invitation)->map(fn (Plan $plan) => [
                ...$plan->only('id', 'name', 'max_guests', 'features'),
                'prices' => collect(self::CURRENCIES)->mapWithKeys(fn ($c) => [$c => Payment::format($this->amount($invitation, $plan, $c), $c)]),
                // Premium-only things in use that this plan won't show, listed before paying for it.
                'beyond' => $limits->beyond($plan),
            ])->values(),
            'pending' => $pending ? [
                'id' => $pending->id,
                'reference' => $pending->reference,
                'plan' => $pending->plan->name,
                'amount' => $pending->amount(),
                'currency' => $pending->currency,
                'has_proof' => $pending->proof_media_id !== null,
            ] : null,
            'rejected' => $rejected && (! $pending || $rejected->id > $pending->id) ? $rejected->only('reference', 'note') : null,
            'instructions' => Setting::read('payment_instructions'),
            'khqr' => Setting::read('payment_khqr', []), // {USD: url, KHR: url}, set in admin settings
        ]);
    }

    public function store(Request $request, Invitation $invitation): RedirectResponse
    {
        $plans = $this->upgradable($invitation);
        $data = $request->validate([
            'plan_id' => ['required', Rule::in($plans->pluck('id'))],
            'currency' => ['required', Rule::in(self::CURRENCIES)],
        ]);
        $plan = $plans->firstWhere('id', $data['plan_id']);

        DB::transaction(function () use ($request, $invitation, $plan, $data) {
            // One open request at a time: choosing again replaces the one not yet reviewed.
            $invitation->payments()->where('status', Payment::PENDING)->get()->each(function (Payment $old) {
                $old->proof?->delete();
                $old->delete();
            });

            $payment = $invitation->payments()->make([
                'plan_id' => $plan->id,
                'amount_minor' => $this->amount($invitation, $plan, $data['currency']),
                'currency' => $data['currency'],
            ]);
            $payment->user()->associate($request->user())->save();
            $payment->forceFill(['reference' => 'KW-'.$payment->id])->save();
        });

        return back();
    }

    /** The screenshot of the transfer, kept on the private disk; only an admin can open it. */
    public function proof(Request $request, Invitation $invitation, Payment $payment): RedirectResponse
    {
        abort_unless($payment->status === Payment::PENDING, 404);
        $request->validate(['file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240']]);

        $old = $payment->proof;
        $media = $invitation->media()->create([
            'kind' => MediaKind::Proof,
            'path' => $request->file('file')->store('payment-proofs', 'local'),
        ]);
        $payment->forceFill(['proof_media_id' => $media->id])->save();
        $old?->delete();

        SendTelegramAlert::dispatch(implode("\n", [
            "💳 Payment {$payment->reference}".($old ? ' (new proof)' : ''),
            "{$payment->plan->name} · {$payment->amount()}",
            "Invitation: /{$invitation->slug}",
            "Couple: {$payment->user->email}",
            'Review: '.url('/admin/payments'),
        ]));

        return back();
    }

    public function destroy(Invitation $invitation, Payment $payment): RedirectResponse
    {
        abort_unless($payment->status === Payment::PENDING, 404);
        $payment->proof?->delete();
        $payment->delete();

        return back();
    }

    /** Active plans above the current one; all of them before the first payment. */
    private function upgradable(Invitation $invitation)
    {
        $current = $invitation->paid_at ? $invitation->plan : null;

        return Plan::where('is_active', true)->orderBy('price_usd_cents')->get()
            ->filter(fn (Plan $plan) => ! $current || $plan->price_usd_cents > $current->price_usd_cents);
    }

    /** Basic → Premium charges the difference. */
    private function amount(Invitation $invitation, Plan $plan, string $currency): int
    {
        $paid = $invitation->paid_at && $invitation->plan ? Payment::price($invitation->plan, $currency) : 0;

        return max(Payment::price($plan, $currency) - $paid, 0);
    }
}
