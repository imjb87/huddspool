<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionStandingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_results_are_excluded_from_standings(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $confirmedFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        Result::factory()->create([
            'fixture_id' => $confirmedFixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 6,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $partialFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $awayTeam->id,
            'away_team_id' => $homeTeam->id,
        ]);

        Result::factory()->create([
            'fixture_id' => $partialFixture->id,
            'home_team_id' => $awayTeam->id,
            'home_team_name' => $awayTeam->name,
            'home_score' => 3,
            'away_team_id' => $homeTeam->id,
            'away_team_name' => $homeTeam->name,
            'away_score' => 2,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => false,
        ]);

        $standings = $section->standings();

        $homeEntry = $standings->firstWhere('id', $homeTeam->id);
        $awayEntry = $standings->firstWhere('id', $awayTeam->id);

        $this->assertSame(1, $homeEntry->played);
        $this->assertSame(1, $homeEntry->wins);
        $this->assertSame(0, $homeEntry->losses);
        $this->assertSame(0, $homeEntry->draws);
        $this->assertSame(6, $homeEntry->points);

        $this->assertSame(1, $awayEntry->played);
        $this->assertSame(0, $awayEntry->wins);
        $this->assertSame(1, $awayEntry->losses);
        $this->assertSame(0, $awayEntry->draws);
        $this->assertSame(4, $awayEntry->points);
    }
}

