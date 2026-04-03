<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use App\Notifications\LeagueNightTonightNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;
use Illuminate\Console\Command;

class SendLeagueNightTonightNotifications extends Command
{
    protected $signature = 'app:send-league-night-tonight-notifications';

    protected $description = 'Send noon reminders for tonight\'s league fixtures';

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
                $audienceResolver->leagueNightRecipientsForFixture($fixture),
                new LeagueNightTonightNotification($fixture),
            );
        }

        $this->info(sprintf('Sent %d league night notifications.', $notificationsSent));

        return self::SUCCESS;
    }
}
