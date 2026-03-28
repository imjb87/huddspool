<?php

namespace Tests\Feature;

use App\Livewire\SectionAverages;
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
use Livewire\Livewire;
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

        $response = $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'averages',
        ]));

        $response->assertOk();
        $response->assertSee('data-section-averages-view', false);
        $response->assertSee('data-section-averages-percentage-badge', false);
        $response->assertSee('inline-flex w-24 cursor-pointer items-center justify-center rounded-full', false);
        $response->assertSee('bg-linear-to-br from-green-900 via-green-800 to-green-700', false);
        $response->assertSeeText($section->name);
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

    public function test_player_averages_match_mixed_home_and_away_results_with_section_filtering(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $otherSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);
        $otherSection->teams()->attach($homeTeam->id, ['sort' => 1]);
        $otherSection->teams()->attach($awayTeam->id, ['sort' => 2]);

        $player = User::factory()->create(['team_id' => $homeTeam->id]);
        $opponent = User::factory()->create(['team_id' => $awayTeam->id]);

        $firstFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);
        $secondFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $awayTeam->id,
            'away_team_id' => $homeTeam->id,
        ]);
        $otherFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $otherSection->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $firstResult = Result::factory()->create([
            'fixture_id' => $firstFixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 1,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 0,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
            'is_confirmed' => true,
        ]);
        $secondResult = Result::factory()->create([
            'fixture_id' => $secondFixture->id,
            'home_team_id' => $awayTeam->id,
            'home_team_name' => $awayTeam->name,
            'home_score' => 1,
            'away_team_id' => $homeTeam->id,
            'away_team_name' => $homeTeam->name,
            'away_score' => 0,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
            'is_confirmed' => true,
        ]);
        $otherResult = Result::factory()->create([
            'fixture_id' => $otherFixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 1,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 0,
            'section_id' => $otherSection->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
            'is_confirmed' => true,
        ]);

        Frame::create([
            'result_id' => $firstResult->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);
        Frame::create([
            'result_id' => $secondResult->id,
            'home_player_id' => $opponent->id,
            'home_score' => 1,
            'away_player_id' => $player->id,
            'away_score' => 0,
        ]);
        Frame::create([
            'result_id' => $otherResult->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);

        $averages = (new GetPlayerAverages($player, $section))();

        $this->assertSame(2, $averages->frames_played);
        $this->assertSame(1, $averages->frames_won);
        $this->assertSame(1, $averages->frames_lost);
        $this->assertSame(50.0, $averages->frames_won_percentage);
        $this->assertSame(50.0, $averages->frames_lost_percentage);
    }

    public function test_section_averages_moves_to_the_next_page_of_players(): void
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

        $opponent = User::factory()->create([
            'name' => 'Common Opponent',
            'team_id' => $awayTeam->id,
        ]);

        foreach (range(1, 12) as $number) {
            $player = User::factory()->create([
                'name' => sprintf('Player %02d', $number),
                'team_id' => $homeTeam->id,
            ]);

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
                'home_score' => 1,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'away_score' => 0,
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
        }

        Livewire::test(SectionAverages::class, ['section' => $section])
            ->assertSee('Player 01')
            ->assertSee('Player 10')
            ->assertDontSee('Player 11')
            ->call('nextPage')
            ->assertSet('page', 2)
            ->assertSee('Player 11')
            ->assertSee('Player 12')
            ->assertDontSee('Player 01');
    }

    public function test_section_averages_does_not_advance_past_the_last_full_page_of_players(): void
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

        $opponent = User::factory()->create([
            'name' => 'Common Opponent',
            'team_id' => $awayTeam->id,
        ]);

        foreach (range(1, 9) as $number) {
            $player = User::factory()->create([
                'name' => sprintf('Player %02d', $number),
                'team_id' => $homeTeam->id,
            ]);

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
                'home_score' => 1,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'away_score' => 0,
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
        }

        Livewire::test(SectionAverages::class, ['section' => $section])
            ->call('nextPage')
            ->assertSet('page', 1);
    }
}
