<?php

namespace App\Queries;

use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GetTeamKnockoutMatches
{
    public function __construct(protected Team $team) {}

    public function __invoke(): Collection
    {
        return KnockoutMatch::query()
            ->with([
                'round.knockout',
                'homeParticipant',
                'awayParticipant',
                'winner',
            ])
            ->whereHas('round', fn (Builder $query) => $query->where('is_visible', true))
            ->whereHas('round.knockout', fn (Builder $query) => $query->where('type', KnockoutType::Team))
            ->where(function (Builder $query) {
                $query->whereHas('homeParticipant', fn (Builder $participantQuery) => $participantQuery->where('team_id', $this->team->id))
                    ->orWhereHas('awayParticipant', fn (Builder $participantQuery) => $participantQuery->where('team_id', $this->team->id));
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get()
            ->values();
    }
}
