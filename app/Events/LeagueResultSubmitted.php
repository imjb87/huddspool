<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeagueResultSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array{
     *     fixture_id: int,
     *     result_id: int,
     *     draft_version: int,
     *     frames: array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>,
     *     home_score: int,
     *     away_score: int,
     *     updated_by_id: ?int,
     *     updated_by_name: ?string,
     *     client_id: string,
     *     is_confirmed: bool,
     *     result_url: string
     * }  $payload
     */
    public function __construct(public array $payload) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('fixture-results.'.$this->payload['fixture_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'league-result.submitted';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
