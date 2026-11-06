<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialEndingSoon extends Notification
{
    public function __construct(public Invitation $invitation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ends = $this->invitation->trial_ends_at->timezone('Asia/Phnom_Penh')->format('j M, H:i');

        return (new MailMessage)
            ->subject('Your free preview ends soon · ការមើលសាកល្បងជិតផុតកំណត់')
            ->line("The free preview of /{$this->invitation->slug} ends on {$ends} (Phnom Penh time).")
            ->line('After that, guests opening your links see a notice instead of the invitation. Upgrade to keep them working; nothing you entered is lost.')
            ->line('ការមើលសាកល្បងនឹងផុតកំណត់ឆាប់ៗនេះ។ សូមដំឡើងកញ្ចប់ ដើម្បីឱ្យតំណរបស់ភ្ញៀវនៅតែដំណើរការ។')
            ->action('Upgrade · ដំឡើងកញ្ចប់', route('invitations.upgrade', $this->invitation));
    }
}
