<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_profile_displays_fixtures_and_players(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create();
        $opponent = Team::factory()->create();

        // attach teams to section
        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponent->id, ['sort' => 2]);

        // create a fixture and result for the team
        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
        ]);

        $user = User::factory()->create(['team_id' => $team->id]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 6,
            'away_team_id' => $opponent->id,
            'away_team_name' => $opponent->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('team.show', $team));

        $response->assertStatus(200);
        $response->assertSeeText('Team profile');
        $response->assertSeeText('Players');
        $response->assertSeeTextInOrder(['Name', 'Pl', 'W', 'L']);
        $response->assertSeeTextInOrder([$team->name, $opponent->name]);
        $response->assertSeeText((string) $result->home_score);
        $response->assertSeeText((string) $result->away_score);
        $response->assertSeeText($user->name);
    }
}
