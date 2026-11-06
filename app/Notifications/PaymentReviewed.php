<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Tells the couple their payment was approved (the invitation is live) or rejected, with your reason. */
class PaymentReviewed extends Notification
{
    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->payment;
        $mail = (new MailMessage)->action('Open Mongkol', route('invitations.upgrade', $p->invitation_id));

        if ($p->status === Payment::APPROVED) {
            return $mail->subject("Your invitation is live · ធៀបការរបស់អ្នកដំណើរការហើយ ({$p->reference})")
                ->line("We received {$p->amount()} for the {$p->plan->name} plan ({$p->reference}). Thank you!")
                ->line('Your invitation is now live: the Preview ribbon is gone and every link works until '
                    .$p->invitation->live_until?->timezone('Asia/Phnom_Penh')->toFormattedDateString().'.')
                ->line('យើងបានទទួលការទូទាត់របស់អ្នក។ ធៀបការរបស់អ្នកដំណើរការពេញលេញហើយ។');
        }

        return $mail->subject("Payment not approved · ការទូទាត់មិនត្រូវបានអនុម័ត ({$p->reference})")
            ->line("We couldn't approve your payment {$p->reference} ({$p->amount()}).")
            ->line("Reason: {$p->note}")
            ->line('Please check it and send a new payment proof from the Upgrade page.')
            ->line('សូមពិនិត្យ ហើយផ្ញើភស្តុតាងនៃការទូទាត់ម្ដងទៀត។');
    }
}
