<?php

namespace App\Queries;

use App\Data\PlayerFrameData;
use App\Models\Frame;
use App\Models\Section;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetPlayerFrames
{
    private const int PER_PAGE = 5;

    public function __construct(
        protected User $player,
        protected ?Section $section = null,
        protected int $page = 1,
    ) {}

    /**
     * @return LengthAwarePaginator<int, PlayerFrameData>
     */
    public function __invoke(): LengthAwarePaginator
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
            ->orderByDesc('fixtures.fixture_date')
            ->orderByDesc('frames.id');

        if ($this->section) {
            $query->where('results.section_id', $this->section->id);
        }

        return $query
            ->paginate(
                perPage: self::PER_PAGE,
                pageName: 'framesPage',
                page: $this->page,
            )
            ->through(fn (Frame $frame) => PlayerFrameData::fromFrame($frame))
            ->withQueryString();
    }
}
