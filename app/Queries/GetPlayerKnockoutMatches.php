<?php

namespace App\Queries;

use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GetPlayerKnockoutMatches
{
    public function __construct(protected User $player) {}

    public function __invoke(): Collection
    {
        $player = $this->player;

        return KnockoutMatch::query()
            ->with([
                'round.knockout',
                'homeParticipant',
                'awayParticipant',
                'venue',
                'forfeitParticipant',
                'winner',
            ])
            ->whereHas('round', fn (Builder $query) => $query->where('is_visible', true))
            ->whereHas('round.knockout', fn (Builder $query) => $query->whereIn('type', [
                KnockoutType::Singles->value,
                KnockoutType::Doubles->value,
            ]))
            ->where(function (Builder $query) use ($player) {
                $query->whereHas('homeParticipant', function (Builder $participantQuery) use ($player) {
                    $this->applyPlayerParticipantFilter($participantQuery, $player);
                })->orWhereHas('awayParticipant', function (Builder $participantQuery) use ($player) {
                    $this->applyPlayerParticipantFilter($participantQuery, $player);
                });
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get()
            ->values();
    }

    private function applyPlayerParticipantFilter(Builder $query, User $player): void
    {
        $query->where(function (Builder $participantQuery) use ($player) {
            $participantQuery->where('player_one_id', $player->id)
                ->orWhere('player_two_id', $player->id);

            if ($player->team_id) {
                $participantQuery->orWhere('team_id', $player->team_id);
            }
        });
    }
}
