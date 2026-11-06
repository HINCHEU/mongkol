<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/** A message to your own Telegram chat. Without a bot token or chat id it only logs, so nothing breaks. */
class SendTelegramAlert implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $text) {}

    public function handle(): void
    {
        $token = config('services.telegram.bot_token');
        $chat = Setting::read('telegram_chat_id') ?: config('services.telegram.chat_id');

        if (! $token || ! $chat) {
            Log::info('Telegram alert (not configured): '.$this->text);

            return;
        }

        try {
            Http::timeout(5)->asForm()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", ['chat_id' => $chat, 'text' => $this->text])
                ->throw();
        } catch (Throwable $e) {
            // The queue is sync in production: a Telegram outage must not fail the couple's upload.
            report($e);
        }
    }
}
