<?php

namespace App\Observers;

use App\Models\Team;
use App\Support\CompetitionCacheInvalidator;
use App\Support\SiteSearch\SearchableRelationRefresher;

class TeamObserver
{
    public function __construct(
        private readonly SearchableRelationRefresher $searchableRelationRefresher,
    ) {
    }

    public function saved(Team $team): void
    {
        $this->flushCaches($team);
        $this->searchableRelationRefresher->refreshTeam($team);
    }

    public function deleted(Team $team): void
    {
        $this->flushCaches($team);
        $this->searchableRelationRefresher->refreshTeam($team);
    }

    public function restored(Team $team): void
    {
        $this->flushCaches($team);
        $this->searchableRelationRefresher->refreshTeam($team);
    }

    protected function flushCaches(Team $team): void
    {
        (new CompetitionCacheInvalidator)->forgetForTeam($team);
    }
}
