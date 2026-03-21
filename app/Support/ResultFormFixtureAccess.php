<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Support\Facades\Gate;

class ResultFormFixtureAccess
{
    public function load(Fixture $fixture): Fixture
    {
        return Fixture::query()
            ->with([
                'section',
                'venue',
                'homeTeam.players',
                'awayTeam.players',
                'result.frames' => fn ($query) => $query->orderBy('id'),
            ])
            ->findOrFail($fixture->getKey());
    }

    public function ensureAccessible(Fixture $fixture): void
    {
        if ($fixture->isBye()) {
            abort(404);
        }

        Gate::authorize('submitResult', $fixture);

        if ($fixture->fixture_date->gte(now())) {
            abort(404);
        }
    }

    public function ensureSubmittable(Fixture $fixture): ?Result
    {
        $this->ensureAccessible($fixture);

        $result = $fixture->result;

        if ($result?->is_confirmed) {
            abort(404);
        }

        return $result;
    }
}
