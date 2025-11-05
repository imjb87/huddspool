<?php

namespace Tests\Feature;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_index_lists_archived_rulesets(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'Eight Ball']);
        $season = Season::factory()->create([
            'name' => '2022/23 Season',
            'is_open' => false,
        ]);
        Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
        ]);

        $response = $this->get(route('history.index'));

        $response->assertOk();
        $response->assertSeeText('History');
        $response->assertSeeText('2022/23 Season');
        $response->assertSeeText('Eight Ball');
    }

    public function test_history_show_displays_section_standings(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'World Rules']);
        $season = Season::factory()->create([
            'name' => '2021/22 Season',
            'is_open' => false,
        ]);
        $section = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division A',
        ]);

        $teamA = Team::factory()->create(['name' => 'Reds']);
        $teamB = Team::factory()->create(['name' => 'Blues']);
        $section->teams()->attach([$teamA->id => ['sort' => 1], $teamB->id => ['sort' => 2]]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $teamA->id,
            'away_team_id' => $teamB->id,
        ]);

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $teamA->id,
            'home_team_name' => $teamA->name,
            'home_score' => 6,
            'away_team_id' => $teamB->id,
            'away_team_name' => $teamB->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $response = $this->get(route('history.show', [$season, $ruleset]));

        $response->assertOk();
        $response->assertSeeText('Division A');
        $response->assertSeeText('Reds');
        $response->assertSeeText('Players');
    }
}
