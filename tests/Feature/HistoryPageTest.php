<?php

namespace Tests\Feature;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Frame;
use App\Models\User;
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
        $openSeason = Season::factory()->create([
            'name' => '2023/24 Season',
            'is_open' => true,
        ]);
        Section::factory()->create([
            'season_id' => $openSeason->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $response = $this->get(route('history.index'));

        $response->assertOk();
        $response->assertSeeText('History');
        $response->assertSeeText('2022/23 Season');
        $response->assertSeeText('Eight Ball');
        $response->assertDontSee('2023/24 Season');
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

    public function test_history_season_overview_displays_section_and_average_winners(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'Blackball']);
        $season = Season::factory()->create([
            'name' => '2020/21 Season',
            'is_open' => false,
        ]);
        $section = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division 1',
        ]);

        $teamA = Team::factory()->create(['name' => 'Champions']);
        $teamB = Team::factory()->create(['name' => 'Contenders']);
        $section->teams()->attach([
            $teamA->id => ['sort' => 1],
            $teamB->id => ['sort' => 2],
        ]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $teamA->id,
            'away_team_id' => $teamB->id,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $teamA->id,
            'home_team_name' => $teamA->name,
            'home_score' => 7,
            'away_team_id' => $teamB->id,
            'away_team_name' => $teamB->name,
            'away_score' => 3,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $playerA = User::factory()->create(['name' => 'Top Player']);
        $playerB = User::factory()->create(['name' => 'Runner Up']);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $playerA->id,
            'home_score' => 2,
            'away_player_id' => $playerB->id,
            'away_score' => 0,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $playerA->id,
            'home_score' => 2,
            'away_player_id' => $playerB->id,
            'away_score' => 1,
        ]);

        $response = $this->get(route('history.season', $season));

        $response->assertOk();
        $response->assertSeeText('Season overview');
        $response->assertSeeText('Division 1');
        $response->assertSeeText('Champions');
        $response->assertSeeText('Top Player');
    }
}
