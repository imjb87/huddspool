<?php

namespace App\Observers;

use App\Domain\Competition\Cache\CompetitionCacheInvalidator;
use App\Models\KnockoutParticipant;

class KnockoutParticipantObserver
{
    public function saved(KnockoutParticipant $participant): void
    {
        $this->flushCaches($participant);
    }

    public function deleted(KnockoutParticipant $participant): void
    {
        $this->flushCaches($participant);
    }

    protected function flushCaches(KnockoutParticipant $participant): void
    {
        if (! $participant->knockout) {
            $participant->loadMissing('knockout');
        }

        if ($participant->knockout) {
            (new CompetitionCacheInvalidator)->forgetForKnockout($participant->knockout);
        }
    }
}
