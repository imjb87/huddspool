<?php

namespace App\Observers;

use App\Models\Knockout;
use App\Support\CompetitionCacheInvalidator;

class KnockoutObserver
{
    public function saved(Knockout $knockout): void
    {
        $this->flushCaches($knockout);
    }

    public function deleted(Knockout $knockout): void
    {
        $this->flushCaches($knockout);
    }

    protected function flushCaches(Knockout $knockout): void
    {
        (new CompetitionCacheInvalidator)->forgetForKnockout($knockout);
    }
}
