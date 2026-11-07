<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\MediaKind;
use App\Http\Controllers\Controller;
use App\Jobs\SendTelegramAlert;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Services\Bakong;
use App\Services\PlanLimits;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Pick a plan and currency, then pay one of two ways:
 * - Bakong (when configured): a dynamic KHQR for the exact amount; `payments:check-bakong` approves it once paid.
 * - Manual: pay your KHQR with the KW-{id} reference in the note → upload the screenshot → you get a
 *   Telegram alert and approve or reject in admin. Always available, so a Bakong outage never blocks a couple.
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
                'method' => $pending->method,
                // The page polls while a Bakong QR is open, so it turns live without a refresh.
                'khqr' => $pending->khqr ? (new QRCode(new QROptions(['addQuietzone' => true])))->render($pending->khqr) : null,
                'expires_at' => $pending->expires_at?->toIso8601String(),
                'plan_id' => $pending->plan_id,
            ] : null,
            'bakong' => Bakong::enabled(),
            'rejected' => $rejected && (! $pending || $rejected->id > $pending->id) ? $rejected->only('reference', 'note') : null,
            'instructions' => Setting::read('payment_instructions'),
            // Your KHQR per currency, uploaded in admin Settings.
            'khqr' => collect(Setting::read('payment_khqr', []))->map(fn ($v) => Setting::imageUrl($v)),
        ]);
    }

    public function store(Request $request, Invitation $invitation): RedirectResponse
    {
        $plans = $this->upgradable($invitation);
        $data = $request->validate([
            'plan_id' => ['required', Rule::in($plans->pluck('id'))],
            'currency' => ['required', Rule::in(self::CURRENCIES)],
            'method' => ['nullable', Rule::in([Payment::MANUAL])], // "pay manually instead"
        ]);
        $plan = $plans->firstWhere('id', $data['plan_id']);

        // One open request at a time: choosing again replaces the one not yet reviewed.
        foreach ($invitation->payments()->where('status', Payment::PENDING)->get() as $old) {
            if ($this->discard($old)) {
                return back(); // that QR was just paid: nothing new to make
            }
        }

        DB::transaction(function () use ($request, $invitation, $plan, $data) {

            $payment = $invitation->payments()->make([
                'plan_id' => $plan->id,
                'amount_minor' => $this->amount($invitation, $plan, $data['currency']),
                'currency' => $data['currency'],
            ]);
            $payment->user()->associate($request->user())->save();
            $payment->forceFill(['reference' => 'KW-'.$payment->id])->save();

            if (Bakong::enabled() && ($data['method'] ?? null) !== Payment::MANUAL) {
                Bakong::issue($payment);
            }
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
        $this->discard($payment);

        return back();
    }

    /**
     * Deletes a pending payment, unless its Bakong QR turns out to be paid already (then it's approved).
     * shortcut: if Bakong can't be reached, the QR is deleted unchecked; a couple who paid it in that
     * moment shows up in your Bakong history without an invitation going live. Fine at this volume.
     */
    private function discard(Payment $payment): bool
    {
        if ($payment->method === Payment::BAKONG && $payment->md5 && Bakong::enabled()) {
            try {
                if (Bakong::settle($payment)) {
                    return true;
                }
            } catch (Throwable $e) {
                report($e);
            }
        }

        $payment->proof?->delete();
        $payment->delete();

        return false;
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
