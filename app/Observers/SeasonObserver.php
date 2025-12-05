<?php

namespace App\Observers;

use App\Models\Season;
use Illuminate\Support\Facades\Cache;

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
        Cache::forget('stats:open-season');
        Cache::forget('nav:past-seasons');
        Cache::forget('history:index');
        Cache::forget(sprintf('history:season:%d', $season->id));

        $season->loadMissing('sections');
        foreach ($season->sections as $section) {
            Cache::forget(sprintf('section:%d:averages', $section->id));
            Cache::forget(sprintf('section:%d:standings', $section->id));

            if ($section->ruleset_id) {
                Cache::forget(sprintf('history:sections:%d:%d', $season->id, $section->ruleset_id));
            }
        }

    }
}
