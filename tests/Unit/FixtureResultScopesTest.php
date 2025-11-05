<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FixtureResultScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixture_for_team_scope_includes_home_and_away_fixtures(): void
    {
        $team = Team::factory()->create();
        $opponent = Team::factory()->create();
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeFixture = Fixture::factory()->create([
            'week' => 1,
            'fixture_date' => Carbon::now(),
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $awayFixture = Fixture::factory()->create([
            'week' => 2,
            'fixture_date' => Carbon::now()->addWeek(),
            'home_team_id' => $opponent->id,
            'away_team_id' => $team->id,
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $ignoredFixture = Fixture::factory()->create([
            'home_team_id' => $opponent->id,
            'away_team_id' => Team::factory()->create()->id,
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $fixtureIds = Fixture::query()->forTeam($team)->pluck('id');

        $this->assertTrue($fixtureIds->contains($homeFixture->id));
        $this->assertTrue($fixtureIds->contains($awayFixture->id));
        $this->assertFalse($fixtureIds->contains($ignoredFixture->id));
    }

    public function test_fixture_in_open_season_scope_only_returns_open_season_fixtures(): void
    {
        $openSeason = Season::factory()->create(['is_open' => true]);
        $closedSeason = Season::factory()->create(['is_open' => false]);
        $ruleset = Ruleset::factory()->create();
        $openSection = Section::factory()->create([
            'season_id' => $openSeason->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $closedSection = Section::factory()->create([
            'season_id' => $closedSeason->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $openFixture = Fixture::factory()->create([
            'season_id' => $openSeason->id,
            'section_id' => $openSection->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $closedFixture = Fixture::factory()->create([
            'season_id' => $closedSeason->id,
            'section_id' => $closedSection->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $fixtureIds = Fixture::query()->inOpenSeason()->pluck('id');

        $this->assertTrue($fixtureIds->contains($openFixture->id));
        $this->assertFalse($fixtureIds->contains($closedFixture->id));
    }

    public function test_result_for_team_scope_matches_home_or_away_results(): void
    {
        $team = Team::factory()->create();
        $opponent = Team::factory()->create();
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $user = User::factory()->create();

        $fixture = Fixture::factory()->create([
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $teamResult = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 5,
            'away_team_id' => $opponent->id,
            'away_team_name' => $opponent->name,
            'away_score' => 3,
            'submitted_by' => $user->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $otherFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Result::factory()->create([
            'fixture_id' => $otherFixture->id,
            'submitted_by' => $user->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $resultIds = Result::query()->forTeam($team)->pluck('id');

        $this->assertTrue($resultIds->contains($teamResult->id));
        $this->assertCount(1, $resultIds);
    }

    public function test_result_in_open_season_scope_filters_by_fixture_season(): void
    {
        $openSeason = Season::factory()->create(['is_open' => true]);
        $closedSeason = Season::factory()->create(['is_open' => false]);
        $ruleset = Ruleset::factory()->create();
        $openSection = Section::factory()->create([
            'season_id' => $openSeason->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $closedSection = Section::factory()->create([
            'season_id' => $closedSeason->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $user = User::factory()->create();

        $openFixture = Fixture::factory()->create([
            'season_id' => $openSeason->id,
            'section_id' => $openSection->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $closedFixture = Fixture::factory()->create([
            'season_id' => $closedSeason->id,
            'section_id' => $closedSection->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $openResult = Result::factory()->create([
            'fixture_id' => $openFixture->id,
            'submitted_by' => $user->id,
            'section_id' => $openSection->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $closedResult = Result::factory()->create([
            'fixture_id' => $closedFixture->id,
            'submitted_by' => $user->id,
            'section_id' => $closedSection->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $resultIds = Result::query()->inOpenSeason()->pluck('id');

        $this->assertTrue($resultIds->contains($openResult->id));
        $this->assertFalse($resultIds->contains($closedResult->id));
    }
}
