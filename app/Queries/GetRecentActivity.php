<?php

namespace App\Queries;

use App\Data\HomeActivityItem;
use App\Models\Result;
use Illuminate\Support\Collection;

class GetRecentActivity
{
    public function __invoke(int $limit = 6): Collection
    {
        return Result::query()
            ->with([
                'fixture.section',
                'fixture.homeTeam',
                'fixture.awayTeam',
            ])
            ->whereHas('fixture.season', fn ($query) => $query->where('is_open', true))
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Result $result) => HomeActivityItem::fromResult($result));
    }
}

