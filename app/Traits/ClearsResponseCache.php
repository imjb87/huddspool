<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;

trait ClearsResponseCache
{
    public static function bootClearsResponseCache()
    {
        self::created(function () {
            ResponseCache::forget('/tables/{$this->ruleset->slug}/');
            ResponseCache::forget('/fixtures-and-results/{$this->ruleset->slug}/');
            ResponseCache::forget('/players/averages/{$this->ruleset->slug}/');
        });

        self::updated(function () {
            ResponseCache::forget('/tables/{$this->ruleset->slug}/');
            ResponseCache::forget('/fixtures-and-results/{$this->ruleset->slug}/');
            ResponseCache::forget('/players/averages/{$this->ruleset->slug}/');
        });

        self::deleted(function () {
            ResponseCache::forget('/tables/{$this->ruleset->slug}/');
            ResponseCache::forget('/fixtures-and-results/{$this->ruleset->slug}/');
            ResponseCache::forget('/players/averages/{$this->ruleset->slug}/');
        });
    }
}