<?php

namespace App\Support\Notifications;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class BrowserPushSender
{
    public function isConfigured(): bool
    {
        return filled(config('services.web_push.subject'))
            && filled(config('services.web_push.public_key'))
            && filled(config('services.web_push.private_key'));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, expired: bool}
     */
    public function send(PushSubscription $subscription, array $payload): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'expired' => false];
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('services.web_push.subject'),
                    'publicKey' => config('services.web_push.public_key'),
                    'privateKey' => config('services.web_push.private_key'),
                ],
            ]);

            $report = $webPush->sendOneNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding ?: 'aes128gcm',
                ]),
                json_encode($this->payloadForBrowser($payload), JSON_THROW_ON_ERROR),
                ['TTL' => 60 * 60 * 12],
            );

            return [
                'success' => $report->isSuccess(),
                'expired' => $report->isSubscriptionExpired(),
            ];
        } catch (Throwable) {
            return ['success' => false, 'expired' => false];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function payloadForBrowser(array $payload): array
    {
        return [
            'title' => data_get($payload, 'title', config('app.name')),
            'body' => data_get($payload, 'body', ''),
            'url' => data_get($payload, 'action_url', route('account.show')),
            'tag' => data_get($payload, 'dedupe_key'),
            'icon' => asset('images/icons/icon-192-192.png'),
            'badge' => asset('images/icons/icon-96-96.png'),
        ];
    }
}
