<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class PurgeOldNotifications extends Command
{
    protected $signature = 'app:purge-old-notifications';

    protected $description = 'Purge notifications older than two weeks';

    public function handle(): int
    {
        $deleted = DatabaseNotification::query()
            ->where('created_at', '<', now()->subWeeks(2))
            ->delete();

        $this->info(sprintf('Purged %d old notifications.', $deleted));

        return self::SUCCESS;
    }
}
