<?php

namespace App\Observers;

use App\Models\KnockoutMatch;
use App\Models\KnockoutRound;
use App\Notifications\KnockoutMatchReadyNotification;
use App\Support\CompetitionCacheInvalidator;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;

class KnockoutRoundObserver
{
    public function saved(KnockoutRound $round): void
    {
        if ($round->wasChanged('scheduled_for') && $round->scheduled_for) {
            $date = $round->scheduled_for->copy();

            $round->matches()->get()->each(function (KnockoutMatch $match) use ($date): void {
                $startsAt = $match->starts_at;

                if ($startsAt) {
                    $updated = $date->copy()->setTime(
                        (int) $startsAt->format('H'),
                        (int) $startsAt->format('i'),
                        (int) $startsAt->format('s')
                    );
                } else {
                    $updated = $date->copy();
                }

                $match->forceFill(['starts_at' => $updated])->saveQuietly();
            });
        }

        if ($round->wasChanged('is_visible') && $round->is_visible) {
            $audienceResolver = new NotificationAudienceResolver;
            $dispatcher = new DatabaseNotificationDispatcher;

            $round->loadMissing([
                'knockout',
                'matches.homeParticipant.team.players',
                'matches.homeParticipant.team.captain',
                'matches.homeParticipant.playerOne',
                'matches.homeParticipant.playerTwo',
                'matches.awayParticipant.team.players',
                'matches.awayParticipant.team.captain',
                'matches.awayParticipant.playerOne',
                'matches.awayParticipant.playerTwo',
            ]);

            $round->matches
                ->filter(fn (KnockoutMatch $match): bool => filled($match->home_participant_id) && filled($match->away_participant_id))
                ->each(function (KnockoutMatch $match) use ($audienceResolver, $dispatcher): void {
                    $dispatcher->sendOnce(
                        $audienceResolver->participantsForKnockoutMatch($match),
                        new KnockoutMatchReadyNotification($match),
                    );
                });
        }

        $round->loadMissing('knockout');

        if ($round->knockout) {
            (new CompetitionCacheInvalidator)->forgetForKnockout($round->knockout);
        }
    }
}
