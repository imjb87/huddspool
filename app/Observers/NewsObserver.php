<?php

namespace App\Observers;

use App\Models\News;
use App\Notifications\NewsPublishedNotification;
use App\Support\CompetitionCacheInvalidator;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;

class NewsObserver
{
    public function creating(News $news): void
    {
        $news->author_id = auth()->id();
    }

    public function saved(News $news): void
    {
        (new CompetitionCacheInvalidator)->forgetForNews();

        if (! $this->wasJustPublished($news)) {
            return;
        }

        app(DatabaseNotificationDispatcher::class)->sendOnce(
            app(NotificationAudienceResolver::class)->usersForPublishedNews(),
            new NewsPublishedNotification($news),
        );
    }

    public function deleted(News $news): void
    {
        (new CompetitionCacheInvalidator)->forgetForNews();
    }

    private function wasJustPublished(News $news): bool
    {
        if (! $news->isPublished()) {
            return false;
        }

        if ($news->wasRecentlyCreated) {
            return true;
        }

        return $news->wasChanged('published_at')
            && blank($news->getOriginal('published_at'));
    }
}
