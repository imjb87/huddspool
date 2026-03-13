<?php

namespace App\Observers;

use App\Models\Season;
use App\Support\CompetitionCacheInvalidator;

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
        app(CompetitionCacheInvalidator::class)->forgetForSeason($season);
    }
}
