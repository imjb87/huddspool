<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use App\Notifications\MatchNightStartedNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;
use Illuminate\Console\Command;

class SendMatchNightStartedNotifications extends Command
{
    protected $signature = 'app:send-match-night-started-notifications';

    protected $description = 'Send 8pm match-night-started reminders for tonight\'s league fixtures';

    public function handle(
        NotificationAudienceResolver $audienceResolver,
        DatabaseNotificationDispatcher $dispatcher,
    ): int {
        $fixtures = Fixture::query()
            ->with([
                'homeTeam.players',
                'homeTeam.captain',
                'awayTeam.players',
                'awayTeam.captain',
            ])
            ->inOpenSeason()
            ->whereHas('homeTeam', fn ($query) => $query->notBye())
            ->whereHas('awayTeam', fn ($query) => $query->notBye())
            ->whereDate('fixture_date', today()->toDateString())
            ->orderBy('fixture_date')
            ->orderBy('id')
            ->get();

        $notificationsSent = 0;

        foreach ($fixtures as $fixture) {
            $notificationsSent += $dispatcher->sendOnce(
                $audienceResolver->teamAdminsForFixture($fixture),
                new MatchNightStartedNotification($fixture),
            );
        }

        $this->info(sprintf('Sent %d match-night-started notifications.', $notificationsSent));

        return self::SUCCESS;
    }
}
