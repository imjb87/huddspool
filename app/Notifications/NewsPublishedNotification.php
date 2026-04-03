<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewsPublishedNotification extends Notification
{
    use Queueable;

    public function __construct(public News $news) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'News published',
            'body' => $this->news->excerpt(140),
            'action_url' => route('news.show', $this->news),
            'news_id' => $this->news->id,
            'dedupe_key' => sprintf('news-published:%d', $this->news->id),
        ];
    }
}
