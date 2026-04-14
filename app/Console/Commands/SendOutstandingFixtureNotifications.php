<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use App\Notifications\FixtureResultOutstandingNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;
use Illuminate\Console\Command;

class SendOutstandingFixtureNotifications extends Command
{
    protected $signature = 'app:send-outstanding-fixture-notifications';

    protected $description = 'Send noon reminders for outstanding league results';

    public function handle(
        NotificationAudienceResolver $audienceResolver,
        DatabaseNotificationDispatcher $dispatcher,
    ): int {
        $fixtures = Fixture::query()
            ->with([
                'result',
                'homeTeam.players',
                'homeTeam.captain',
                'awayTeam.players',
                'awayTeam.captain',
            ])
            ->inOpenSeason()
            ->whereHas('homeTeam', fn ($query) => $query->notBye())
            ->whereHas('awayTeam', fn ($query) => $query->notBye())
            ->whereDate('fixture_date', today()->subDay()->toDateString())
            ->orderBy('fixture_date')
            ->orderBy('id')
            ->get()
            ->filter(fn (Fixture $fixture): bool => ! $fixture->isBye() && ! $fixture->result?->is_confirmed)
            ->values();

        $notificationsSent = 0;

        foreach ($fixtures as $fixture) {
            $notificationsSent += $dispatcher->sendOnce(
                $audienceResolver->teamAdminsForFixture($fixture),
                new FixtureResultOutstandingNotification($fixture),
            );
        }

        $this->info(sprintf('Sent %d outstanding fixture notifications.', $notificationsSent));

        return self::SUCCESS;
    }
}
