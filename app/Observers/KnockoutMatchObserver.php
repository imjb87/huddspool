<?php

namespace App\Observers;

use App\Models\KnockoutMatch;
use App\Support\CompetitionCacheInvalidator;

class KnockoutMatchObserver
{
    public function saved(KnockoutMatch $match): void
    {
        $this->flushCaches($match);
    }

    public function deleted(KnockoutMatch $match): void
    {
        $this->flushCaches($match);
    }

    protected function flushCaches(KnockoutMatch $match): void
    {
        (new CompetitionCacheInvalidator)->forgetForKnockoutMatch($match);
    }
}
