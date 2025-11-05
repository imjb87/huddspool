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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_profile_displays_averages_and_recent_frames(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();

        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponentTeam->id, ['sort' => 2]);

        $player = User::factory()->create(['team_id' => $team->id]);
        $opponent = User::factory()->create(['team_id' => $opponentTeam->id]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 6,
            'away_team_id' => $opponentTeam->id,
            'away_team_name' => $opponentTeam->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $player->id,
            'home_score' => 2,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $opponent->id,
            'home_score' => 2,
            'away_player_id' => $player->id,
            'away_score' => 1,
        ]);

        $this->actingAs($player);

        $response = $this->get(route('player.show', $player));

        $response->assertStatus(200);
        $response->assertSeeText('Player profile');
        $response->assertSeeText($player->name);
        $response->assertSeeText($team->name);
        $response->assertSeeTextInOrder(['Played', 'Won', 'Lost']);
        $response->assertSeeText('50.00%');
        $response->assertSeeText($opponent->name);
        $response->assertSeeTextInOrder(['Frames', $opponentTeam->name]);
    }
}
