<?php

namespace App\Notifications;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeagueResultSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Result $result,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
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
        $this->result->loadMissing([
            'submittedBy',
        ]);

        return [
            'title' => '📋 Result submitted',
            'body' => sprintf(
                '%s submitted %s %d-%d %s.',
                $this->result->submittedBy?->name ?? 'A team admin',
                $this->result->home_team_name,
                (int) $this->result->home_score,
                (int) $this->result->away_score,
                $this->result->away_team_name,
            ),
            'action_url' => route('result.show', $this->result),
            'result_id' => $this->result->id,
            'fixture_id' => $this->result->fixture_id,
            'submitted_by_id' => $this->result->submitted_by,
            'dedupe_key' => sprintf('result-submitted:%d', $this->result->id),
        ];
    }
}
