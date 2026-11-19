<?php

namespace App\Observers;

use App\Domain\Competition\Cache\CompetitionCacheInvalidator;
use App\Models\Team;

class TeamObserver
{
    public function saved(Team $team): void
    {
        $this->flushCaches($team);
    }

    public function deleted(Team $team): void
    {
        $this->flushCaches($team);
    }

    public function restored(Team $team): void
    {
        $this->flushCaches($team);
    }

    protected function flushCaches(Team $team): void
    {
        (new CompetitionCacheInvalidator)->forgetForTeam($team);
    }
}
