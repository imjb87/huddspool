<?php

namespace App\Jobs;

use App\Models\User;
use App\Support\Notifications\BrowserPushSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendBrowserPushNotification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $userId,
        public array $payload,
    ) {}

    public function handle(BrowserPushSender $sender): void
    {
        if (! $sender->isConfigured()) {
            return;
        }

        $user = User::query()
            ->with('pushSubscriptions')
            ->find($this->userId);

        if (! $user instanceof User) {
            return;
        }

        foreach ($user->pushSubscriptions as $subscription) {
            $result = $sender->send($subscription, $this->payload);

            if ($result['expired']) {
                $subscription->delete();

                continue;
            }

            if ($result['success']) {
                $subscription->forceFill([
                    'last_used_at' => now(),
                ])->save();
            }
        }
    }
}
