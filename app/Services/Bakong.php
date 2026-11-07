<?php

namespace App\Services;

use App\Actions\ReviewPayment;
use App\Jobs\SendTelegramAlert;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Konthaina\Khqr\KHQRGenerator;

/**
 * Automatic payment, phase 2: a dynamic KHQR for the exact amount, then Bakong's
 * check_transaction_by_md5 until it's paid (`payments:check-bakong`, every 5 seconds).
 * Manual KHQR plus a screenshot stays the fallback, and is the only way without a token.
 */
class Bakong
{
    public const QR_MINUTES = 10;

    public static function enabled(): bool
    {
        return filled(config('services.bakong.token')) && filled(config('services.bakong.account_id'));
    }

    /** Makes the QR for a saved payment; the KW- reference goes in as the bill number. */
    public static function issue(Payment $payment): void
    {
        $qr = (new KHQRGenerator(KHQRGenerator::MERCHANT_TYPE_INDIVIDUAL))
            ->setBakongAccountId(config('services.bakong.account_id'))
            ->setMerchantName(Str::limit(config('services.bakong.merchant_name'), 25, ''))
            ->setMerchantCity(config('services.bakong.merchant_city'))
            ->setCurrency($payment->currency)
            ->setAmount($payment->currency === 'KHR' ? $payment->amount_minor : $payment->amount_minor / 100)
            ->setBillNumber($payment->reference)
            ->setExpirationDuration(self::QR_MINUTES * 60)
            ->generate();

        $payment->forceFill([
            'method' => Payment::BAKONG,
            'khqr' => $qr['qr'],
            'md5' => $qr['md5'],
            'expires_at' => Carbon::createFromTimestampMs($qr['expirationTimestamp']),
        ])->save();
    }

    /** Asks Bakong once and approves the payment if it was paid in full to our account. */
    public static function settle(Payment $payment): bool
    {
        $response = Http::withToken(config('services.bakong.token'))->timeout(10)->acceptJson()
            ->post(rtrim(config('services.bakong.url'), '/').'/v1/check_transaction_by_md5', ['md5' => $payment->md5])
            ->throw()->json();

        $tx = ($response['responseCode'] ?? null) === 0 ? ($response['data'] ?? null) : null;
        if (! $tx) {
            return false; // not paid (yet)
        }

        // The md5 is of our own QR, but check the money reached our account, in full, before going live.
        $paid = Str::lower($tx['toAccountId'] ?? '') === Str::lower(config('services.bakong.account_id'))
            && ($tx['currency'] ?? null) === $payment->currency
            && (int) round((float) ($tx['amount'] ?? 0) * ($payment->currency === 'KHR' ? 1 : 100)) === $payment->amount_minor;

        if (! $paid) {
            // The note takes it off the 5-second check; you look at it in admin Payments.
            $payment->forceFill(['note' => 'Bakong transaction does not match: '.Str::limit(json_encode($tx), 200)])->save();
            SendTelegramAlert::dispatch("⚠️ Bakong payment {$payment->reference} doesn't match the QR. Check it: ".url('/admin/payments'));

            return false;
        }

        ReviewPayment::approve($payment);
        SendTelegramAlert::dispatch(implode("\n", [
            "✅ Bakong payment {$payment->reference}, approved automatically",
            "{$payment->plan->name} · {$payment->amount()}",
            "Invitation: /{$payment->invitation->slug}",
        ]));

        return true;
    }
}
