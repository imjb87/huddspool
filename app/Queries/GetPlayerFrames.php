<?php

namespace App\Queries;

use App\Data\PlayerFrameData;
use App\Models\Frame;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Collection;

class GetPlayerFrames
{
    public function __construct(
        protected User $player,
        protected ?Section $section = null,
    ) {
    }

    /**
     * @return Collection<int, PlayerFrameData>
     */
    public function __invoke(): Collection
    {
        $query = Frame::query()
            ->select('frames.*')
            ->join('results', 'results.id', '=', 'frames.result_id')
            ->join('fixtures', 'fixtures.id', '=', 'results.fixture_id')
            ->with([
                'homePlayer' => fn ($relation) => $relation->withTrashed(),
                'awayPlayer' => fn ($relation) => $relation->withTrashed(),
                'result.fixture.homeTeam' => fn ($relation) => $relation->withTrashed(),
                'result.fixture.awayTeam' => fn ($relation) => $relation->withTrashed(),
            ])
            ->where(function ($builder) {
                $builder
                    ->where('frames.home_player_id', $this->player->id)
                    ->orWhere('frames.away_player_id', $this->player->id);
            })
            ->orderByDesc('fixtures.fixture_date');

        if ($this->section) {
            $query->where('results.section_id', $this->section->id);
        }

        return $query
            ->get()
            ->map(fn (Frame $frame) => PlayerFrameData::fromFrame($frame));
    }
}
