<?php

namespace App\Observers;

use App\Models\KnockoutMatch;
use App\Models\KnockoutRound;

class KnockoutRoundObserver
{
    public function saved(KnockoutRound $round): void
    {
        if (! $round->wasChanged('scheduled_for') || ! $round->scheduled_for) {
            return;
        }

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
}
