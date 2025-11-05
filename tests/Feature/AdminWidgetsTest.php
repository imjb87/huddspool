<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminWidgetsTest extends TestCase
{
    use RefreshDatabase;

    private function makeSeasonContext(): array
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        // ensure ID 1 is occupied so the widget's exclusions don't affect test teams
        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        return compact('season', 'ruleset', 'section', 'homeTeam', 'awayTeam');
    }

    public function test_outstanding_fixtures_widget_includes_partial_results(): void
    {
        [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
        ] = $this->makeSeasonContext();

        $partialFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => Carbon::yesterday(),
        ]);

        Result::factory()->create([
            'fixture_id' => $partialFixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 3,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 2,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => false,
        ]);

        $unsubmittedFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $awayTeam->id,
            'away_team_id' => $homeTeam->id,
            'fixture_date' => Carbon::yesterday(),
        ]);

        $confirmedFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => Carbon::yesterday(),
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

        $results = Fixture::query()
            ->whereDoesntHave('result', function ($query) {
                $query->where('is_confirmed', true);
            })
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->where('home_team_id', '!=', 1)
            ->where('away_team_id', '!=', 1)
            ->where('fixture_date', '<', now())
            ->pluck('id');

        $this->assertTrue($results->contains($partialFixture->id));
        $this->assertTrue($results->contains($unsubmittedFixture->id));
        $this->assertFalse($results->contains($confirmedFixture->id));
    }

    public function test_latest_results_widget_omits_partial_results(): void
    {
        [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
        ] = $this->makeSeasonContext();

        $confirmed = Result::factory()->create([
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'home_score' => 6,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
            'fixture_id' => Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'fixture_date' => Carbon::now(),
            ])->id,
        ]);

        $partial = Result::factory()->create([
            'home_team_id' => $awayTeam->id,
            'home_team_name' => $awayTeam->name,
            'away_team_id' => $homeTeam->id,
            'away_team_name' => $homeTeam->name,
            'home_score' => 3,
            'away_score' => 3,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => false,
            'fixture_id' => Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $awayTeam->id,
                'away_team_id' => $homeTeam->id,
                'fixture_date' => Carbon::now(),
            ])->id,
        ]);

        $latestResults = Result::query()
            ->where('is_confirmed', true)
            ->whereHas('fixture.season', fn ($query) => $query->where('is_open', true))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->pluck('id');

        $this->assertTrue($latestResults->contains($confirmed->id));
        $this->assertFalse($latestResults->contains($partial->id));
    }
}

