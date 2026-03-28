<?php

namespace Tests\Unit;

use App\Models\Expulsion;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_preloaded_relations_prevent_additional_queries_when_building_standings(): void
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

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 5,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 5,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $section = Section::query()->findOrFail($section->id);
        $section->load([
            'results',
            'season.expulsions',
            'teams' => fn ($query) => $query->withTrashed()->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at']),
        ]);
        $section->forgetStandingsCache();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $standings = $section->standings();

        $this->assertCount(2, $standings);
        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_standings_preserve_order_archived_names_and_expulsions(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $alpha = Team::factory()->create(['name' => 'Alpha']);
        $beta = Team::factory()->create(['name' => 'Beta']);
        $gamma = Team::factory()->create(['name' => 'Gamma']);

        $section->teams()->attach($alpha->id, ['sort' => 1, 'deducted' => 1]);
        $section->teams()->attach($beta->id, ['sort' => 2, 'deducted' => 0]);
        $section->teams()->attach($gamma->id, ['sort' => 3, 'deducted' => 0]);

        $fixtureOne = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $alpha->id,
            'away_team_id' => $beta->id,
        ]);

        Result::factory()->create([
            'id' => 100,
            'fixture_id' => $fixtureOne->id,
            'home_team_id' => $alpha->id,
            'home_team_name' => 'Alpha Old',
            'home_score' => 6,
            'away_team_id' => $beta->id,
            'away_team_name' => 'Beta Old',
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $fixtureTwo = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $gamma->id,
            'away_team_id' => $alpha->id,
        ]);

        Result::factory()->create([
            'id' => 200,
            'fixture_id' => $fixtureTwo->id,
            'home_team_id' => $gamma->id,
            'home_team_name' => 'Gamma Current',
            'home_score' => 5,
            'away_team_id' => $alpha->id,
            'away_team_name' => 'Alpha Current',
            'away_score' => 5,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $fixtureThree = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $beta->id,
            'away_team_id' => $gamma->id,
        ]);

        Result::factory()->create([
            'id' => 300,
            'fixture_id' => $fixtureThree->id,
            'home_team_id' => $beta->id,
            'home_team_name' => 'Beta Current',
            'home_score' => 7,
            'away_team_id' => $gamma->id,
            'away_team_name' => 'Gamma Old',
            'away_score' => 3,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        Expulsion::query()->create([
            'season_id' => $season->id,
            'expellable_id' => $gamma->id,
            'expellable_type' => Team::class,
            'reason' => 'Withdrawn from competition',
            'date' => now(),
        ]);

        $standings = $section->fresh()->standings();

        $this->assertSame([$beta->id, $alpha->id, $gamma->id], $standings->pluck('id')->all());

        $betaEntry = $standings->firstWhere('id', $beta->id);
        $alphaEntry = $standings->firstWhere('id', $alpha->id);
        $gammaEntry = $standings->firstWhere('id', $gamma->id);

        $this->assertSame('Beta Current', $betaEntry->archived_name);
        $this->assertSame(2, $betaEntry->played);
        $this->assertSame(1, $betaEntry->wins);
        $this->assertSame(0, $betaEntry->draws);
        $this->assertSame(1, $betaEntry->losses);
        $this->assertSame(11, $betaEntry->points);

        $this->assertSame('Alpha Current', $alphaEntry->archived_name);
        $this->assertSame(2, $alphaEntry->played);
        $this->assertSame(1, $alphaEntry->wins);
        $this->assertSame(1, $alphaEntry->draws);
        $this->assertSame(0, $alphaEntry->losses);
        $this->assertSame(10, $alphaEntry->points);

        $this->assertSame('Gamma Old', $gammaEntry->archived_name);
        $this->assertTrue($gammaEntry->expelled);
        $this->assertSame(0, $gammaEntry->played);
        $this->assertSame(0, $gammaEntry->wins);
        $this->assertSame(0, $gammaEntry->draws);
        $this->assertSame(0, $gammaEntry->losses);
        $this->assertSame(0, $gammaEntry->points);
    }
}
