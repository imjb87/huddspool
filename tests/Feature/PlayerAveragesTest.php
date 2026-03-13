<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerAveragesTest extends TestCase
{
    use RefreshDatabase;

    public function test_awarded_frames_are_excluded_from_averages_list(): void
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

        $player = User::factory()->create(['name' => 'Real Player', 'team_id' => $homeTeam->id]);
        $opponent = User::factory()->create(['name' => 'Opponent', 'team_id' => $awayTeam->id]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 6,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
            'is_confirmed' => true,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => 0,
            'home_score' => 1,
            'away_player_id' => 0,
            'away_score' => 0,
        ]);

        $response = $this->get(route('player.index', $ruleset));

        $response->assertOk();
        $response->assertSeeText('Averages');
        $response->assertSeeText('Real Player');
        $response->assertDontSeeText('Unknown');
    }

    public function test_soft_deleted_frames_are_excluded_from_player_averages(): void
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

        $player = User::factory()->create(['team_id' => $homeTeam->id]);
        $opponent = User::factory()->create(['team_id' => $awayTeam->id]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 6,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
            'is_confirmed' => true,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $player->id,
            'home_score' => 2,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);

        $trashedFrame = Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $opponent->id,
            'home_score' => 2,
            'away_player_id' => $player->id,
            'away_score' => 1,
        ]);

        $trashedFrame->delete();

        $averages = (new GetPlayerAverages($player, $section))();

        $this->assertSoftDeleted($trashedFrame);
        $this->assertSame(1, $averages->frames_played);
        $this->assertSame(1, $averages->frames_won);
        $this->assertSame(0, $averages->frames_lost);
        $this->assertSame(100.0, $averages->frames_won_percentage);
        $this->assertSame(0.0, $averages->frames_lost_percentage);
    }
}
