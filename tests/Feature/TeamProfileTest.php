<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\KnockoutType;
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
        $response->assertSee('data-team-page', false);
        $response->assertSee('data-team-info-section', false);
        $response->assertSee('data-team-players-section', false);
        $response->assertSee('data-team-fixtures-section', false);
        $response->assertSeeText($team->name);
        $response->assertSeeText('Team information');
        $response->assertSeeText('Players');
        $response->assertSeeText('Fixtures');
        $response->assertSeeTextInOrder([$team->name, $opponent->name]);
        $response->assertSeeText((string) $result->home_score);
        $response->assertSeeText((string) $result->away_score);
        $response->assertSeeText($user->name);
    }

    public function test_team_profile_displays_team_knockout_matches(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create();
        $opponent = Team::factory()->create();

        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponent->id, ['sort' => 2]);

        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Team KO',
            'type' => KnockoutType::Team,
        ]);

        $round = KnockoutRound::query()->create([
            'knockout_id' => $knockout->id,
            'name' => 'Semi-finals',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'team_id' => $team->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'team_id' => $opponent->id,
        ]);

        KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'home_score' => 5,
            'away_score' => 4,
            'winner_participant_id' => $homeParticipant->id,
            'best_of' => 11,
            'starts_at' => now()->subDays(2),
        ]);

        $response = $this->get(route('team.show', $team));

        $response->assertOk();
        $response->assertSee('data-team-knockout-section', false);
        $response->assertSeeText('Team knockouts');
        $response->assertSeeText($knockout->name);
        $response->assertSee(route('knockout.show', $knockout), false);
        $response->assertSeeText('5');
        $response->assertSeeText('4');
    }
}
