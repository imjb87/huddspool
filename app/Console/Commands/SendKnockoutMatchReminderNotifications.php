<?php

namespace App\Console\Commands;

use App\Models\KnockoutMatch;
use App\Notifications\KnockoutMatchReminderNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;
use Illuminate\Console\Command;

class SendKnockoutMatchReminderNotifications extends Command
{
    protected $signature = 'app:send-knockout-match-reminder-notifications';

    protected $description = 'Send noon reminders for knockout matches scheduled for today';

    public function handle(
        NotificationAudienceResolver $audienceResolver,
        DatabaseNotificationDispatcher $dispatcher,
    ): int {
        $matches = KnockoutMatch::query()
            ->with([
                'round.knockout.season',
                'homeParticipant.team.players',
                'homeParticipant.team.captain',
                'homeParticipant.playerOne',
                'homeParticipant.playerTwo',
                'awayParticipant.team.players',
                'awayParticipant.team.captain',
                'awayParticipant.playerOne',
                'awayParticipant.playerTwo',
            ])
            ->whereHas('knockout.season', fn ($query) => $query->where('is_open', true))
            ->whereNull('winner_participant_id')
            ->orderByRaw('case when starts_at is null then 1 else 0 end')
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get()
            ->filter(function (KnockoutMatch $match): bool {
                $scheduledFor = $match->starts_at ?? $match->round?->scheduled_for;

                return $scheduledFor?->isSameDay(today()) ?? false;
            })
            ->values();

        $notificationsSent = 0;

        foreach ($matches as $match) {
            $notificationsSent += $dispatcher->sendOnce(
                $audienceResolver->participantsForKnockoutMatch($match),
                new KnockoutMatchReminderNotification($match),
            );
        }

        $this->info(sprintf('Sent %d knockout match reminder notifications.', $notificationsSent));

        return self::SUCCESS;
    }
}
