<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use App\Notifications\TuesdayResultCatchupNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendTuesdayResultCatchupNotifications extends Command
{
    protected $signature = 'app:send-tuesday-result-catchup-notifications';

    protected $description = 'Send Sunday noon reminders for outstanding results from the previous Tuesday';

    public function handle(
        NotificationAudienceResolver $audienceResolver,
        DatabaseNotificationDispatcher $dispatcher,
    ): int {
        $previousTuesday = today()->previous(Carbon::TUESDAY);

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
            ->whereDate('fixture_date', $previousTuesday->toDateString())
            ->orderBy('fixture_date')
            ->orderBy('id')
            ->get()
            ->filter(fn (Fixture $fixture): bool => ! $fixture->result?->is_confirmed)
            ->values();

        $notificationsSent = 0;

        foreach ($fixtures as $fixture) {
            $notificationsSent += $dispatcher->sendOnce(
                $audienceResolver->teamAdminsForFixture($fixture),
                new TuesdayResultCatchupNotification($fixture, $previousTuesday),
            );
        }

        $this->info(sprintf('Sent %d Tuesday result catch-up notifications.', $notificationsSent));

        return self::SUCCESS;
    }
}
