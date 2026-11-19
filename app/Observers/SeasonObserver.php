<?php

namespace App\Observers;

use App\Domain\Competition\Cache\CompetitionCacheInvalidator;
use App\Models\Season;

class SeasonObserver
{
    public function saved(Season $season): void
    {
        $this->flushCaches($season);
    }

    public function deleted(Season $season): void
    {
        $this->flushCaches($season);
    }

    public function restored(Season $season): void
    {
        $this->flushCaches($season);
    }

    protected function flushCaches(Season $season): void
    {
        (new CompetitionCacheInvalidator)->forgetForSeason($season);
    }
}
