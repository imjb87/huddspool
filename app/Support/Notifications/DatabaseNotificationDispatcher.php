<?php

namespace App\Support\Notifications;

use App\Jobs\SendBrowserPushNotification;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DatabaseNotificationDispatcher
{
    /**
     * @param  Collection<int, User>  $users
     */
    public function sendOnce(Collection $users, Notification $notification): int
    {
        $sent = 0;

        foreach ($users as $user) {
            $payload = $notification->toArray($user);
            $dedupeKey = data_get($payload, 'dedupe_key');

            if (is_string($dedupeKey) && $this->wasAlreadySent($user, $notification::class, $dedupeKey)) {
                continue;
            }

            $user->notify(clone $notification);

            if ($user->pushSubscriptions()->exists()) {
                SendBrowserPushNotification::dispatch($user->id, $payload);
            }

            $sent++;
        }

        return $sent;
    }

    private function wasAlreadySent(User $user, string $notificationType, string $dedupeKey): bool
    {
        return $user->notifications()
            ->where('type', $notificationType)
            ->where('data->dedupe_key', $dedupeKey)
            ->exists();
    }
}
