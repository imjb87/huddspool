<?php

namespace App\Observers;

use App\Models\News;

class NewsObserver
{
    public function creating(News $news): void
    {
        $news->author_id = auth()->id();
    }
}
