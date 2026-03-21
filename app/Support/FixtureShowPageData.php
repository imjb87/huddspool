<?php

namespace App\Support;

use App\Models\Fixture;

class FixtureShowPageData
{
    public function build(Fixture $fixture): object
    {
        $canSubmitResult = auth()->check()
            && auth()->user()->can('submitResult', $fixture)
            && (! $fixture->result || ! $fixture->result->is_confirmed);

        return (object) [
            'can_submit_result' => $canSubmitResult,
            'submission_is_open' => $canSubmitResult && $fixture->fixture_date->lte(now()),
            'standings' => $fixture->section->standings()
                ->filter(function ($standing) use ($fixture) {
                    return (int) $standing->id === (int) $fixture->home_team_id
                        || (int) $standing->id === (int) $fixture->away_team_id;
                })
                ->values(),
        ];
    }
}
