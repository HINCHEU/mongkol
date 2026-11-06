<?php

namespace App\Actions;

use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentReviewed;
use App\Services\Themes\KhmerFormat;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/** Your decision on a pending payment. Approving is the only way an invitation goes live. */
class ReviewPayment
{
    public static function approve(Payment $payment, User $admin): void
    {
        self::assertPending($payment);

        DB::transaction(function () use ($payment, $admin) {
            $payment->forceFill(['status' => Payment::APPROVED, 'reviewed_by' => $admin->id, 'reviewed_at' => now()])->save();

            $invitation = $payment->invitation;
            $days = $payment->plan->live_days_after_wedding;
            // Saving the invitation clears its cached view, so the ribbon is gone on the next load.
            $invitation->forceFill([
                'paid_at' => $invitation->paid_at ?? now(),
                'plan_id' => $payment->plan_id,
                // From the wedding date at approval; if the date moves later, use "Set live-until" in Invitations.
                'live_until' => $invitation->wedding_date->copy()->shiftTimezone(KhmerFormat::TIMEZONE)->endOfDay()->addDays($days)->utc(),
            ])->save();

            ActivityLog::record('payment.approved', $payment, $invitation->only('plan_id', 'live_until'), $admin);
        });

        $payment->user->notify(new PaymentReviewed($payment->fresh()));
    }

    public static function reject(Payment $payment, User $admin, string $reason): void
    {
        self::assertPending($payment);

        $payment->forceFill(['status' => Payment::REJECTED, 'reviewed_by' => $admin->id, 'reviewed_at' => now(), 'note' => $reason])->save();
        ActivityLog::record('payment.rejected', $payment, ['reason' => $reason], $admin);
        $payment->user->notify(new PaymentReviewed($payment));
    }

    private static function assertPending(Payment $payment): void
    {
        if ($payment->status !== Payment::PENDING) {
            throw new InvalidArgumentException("Payment {$payment->reference} was already {$payment->status}.");
        }
    }
}
