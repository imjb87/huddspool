<?php

namespace App\Data;

use App\Models\Result;
use Illuminate\Support\Carbon;

final class HomeActivityItem
{
    public function __construct(
        public int $result_id,
        public int $fixture_id,
        public int $home_team_id,
        public int $away_team_id,
        public string $home_team_name,
        public string $away_team_name,
        public ?string $home_team_shortname,
        public ?string $away_team_shortname,
        public int $home_score,
        public int $away_score,
        public bool $is_confirmed,
        public string $section_name,
        public ?Carbon $fixture_date,
        public Carbon $updated_at,
    ) {
    }

    public static function fromResult(Result $result): self
    {
        $fixture = $result->fixture;
        $homeTeam = $fixture?->homeTeam;
        $awayTeam = $fixture?->awayTeam;
        $sectionName = $fixture?->section?->name ?? 'Unknown Section';

        return new self(
            result_id: $result->id,
            fixture_id: $fixture?->id ?? $result->fixture_id,
            home_team_id: $result->home_team_id,
            away_team_id: $result->away_team_id,
            home_team_name: $result->home_team_name ?? $homeTeam?->name ?? 'Home',
            away_team_name: $result->away_team_name ?? $awayTeam?->name ?? 'Away',
            home_team_shortname: $homeTeam?->shortname,
            away_team_shortname: $awayTeam?->shortname,
            home_score: (int) $result->home_score,
            away_score: (int) $result->away_score,
            is_confirmed: (bool) $result->is_confirmed,
            section_name: $sectionName,
            fixture_date: $fixture?->fixture_date,
            updated_at: $result->updated_at instanceof Carbon ? $result->updated_at : Carbon::parse($result->updated_at),
        );
    }
}

