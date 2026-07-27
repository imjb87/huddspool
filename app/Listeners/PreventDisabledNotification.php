<?php

namespace App\Listeners;

use App\Models\NotificationSetting;
use Illuminate\Notifications\Events\NotificationSending;

class PreventDisabledNotification
{
    public function handle(NotificationSending $event): bool
    {
        return NotificationSetting::isEnabledFor($event->notification::class);
    }
}
