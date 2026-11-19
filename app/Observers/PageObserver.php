<?php

namespace App\Observers;

use App\Domain\Competition\Cache\CompetitionCacheInvalidator;
use App\Models\Page;

class PageObserver
{
    public function saved(Page $page): void
    {
        $this->flushCaches($page);
    }

    public function deleted(Page $page): void
    {
        $this->flushCaches($page);
    }

    protected function flushCaches(Page $page): void
    {
        (new CompetitionCacheInvalidator)->forgetForPage($page);
    }
}
