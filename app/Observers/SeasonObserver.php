<?php

namespace App\Observers;

use App\Models\Season;
use App\Support\CompetitionCacheInvalidator;
use App\Support\SiteSearch\SearchableRelationRefresher;

class SeasonObserver
{
    public function __construct(
        private readonly SearchableRelationRefresher $searchableRelationRefresher,
    ) {
    }

    public function saved(Season $season): void
    {
        $this->flushCaches($season);
        $this->searchableRelationRefresher->refreshSeason($season);
    }

    public function deleted(Season $season): void
    {
        $this->flushCaches($season);
        $this->searchableRelationRefresher->refreshSeason($season);
    }

    public function restored(Season $season): void
    {
        $this->flushCaches($season);
        $this->searchableRelationRefresher->refreshSeason($season);
    }

    protected function flushCaches(Season $season): void
    {
        (new CompetitionCacheInvalidator)->forgetForSeason($season);
    }
}
