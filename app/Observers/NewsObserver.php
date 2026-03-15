<?php

namespace App\Observers;

use App\Models\News;
use App\Support\CompetitionCacheInvalidator;

class NewsObserver
{
    public function creating(News $news): void
    {
        $news->author_id = auth()->id();
    }

    public function saved(News $news): void
    {
        app(CompetitionCacheInvalidator::class)->forgetForNews();
    }

    public function deleted(News $news): void
    {
        app(CompetitionCacheInvalidator::class)->forgetForNews();
    }
}
