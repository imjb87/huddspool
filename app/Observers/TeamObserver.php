<?php

namespace App\Observers;

use App\Models\Team;
use App\Support\CompetitionCacheInvalidator;

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
        app(CompetitionCacheInvalidator::class)->forgetForTeam($team);
    }
}
