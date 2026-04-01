<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\KnockoutType;
use App\Livewire\Team\FixturesSection;
use App\Livewire\Team\HistorySection;
use App\Livewire\Team\PlayersSection;
use App\Models\Expulsion;
use App\Models\Fixture;
use App\Models\Frame;
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
use App\Queries\GetTeamPlayers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        Team::factory()->create();
        $team = Team::factory()->create(['shortname' => 'TEAM']);
        $opponent = Team::factory()->create(['shortname' => 'OPP']);

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

        $user = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

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
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-section-shared-header', false);
        $response->assertSee('dark:bg-neutral-950', false);
        $response->assertSee('dark:border-neutral-800/80', false);
        $response->assertSee('dark:text-gray-100', false);
        $response->assertSee('data-team-info-section', false);
        $response->assertSee('data-team-players-section', false);
        $response->assertSee('data-team-fixtures-section', false);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSeeLivewire(FixturesSection::class);
        $response->assertSeeLivewire(HistorySection::class);
        $response->assertSeeLivewire(PlayersSection::class);
        $response->assertSeeText($team->name);
        $response->assertSeeText('Team');
        $response->assertSeeText('Team information');
        $response->assertSeeText('Players');
        $response->assertSeeText('Fixtures');
        $response->assertSeeText('Current standing');
        $response->assertSeeText('1st of 2');
        $response->assertSeeText('6 pts from 1 played');
        $response->assertSeeTextInOrder([$team->name, $opponent->name]);
        $response->assertSeeText('TEAM');
        $response->assertSeeText('OPP');
        $response->assertSee('sm:hidden', false);
        $response->assertSee('sm:block', false);
        $response->assertSeeText((string) $result->home_score);
        $response->assertSeeText((string) $result->away_score);
        $response->assertSeeText($user->name);
        $response->assertSeeText(UserRole::labelFor($user->role));
        $response->assertSeeText('0%');
        $response->assertSee('href="'.route('result.show', $result).'"', false);
    }

    public function test_team_profile_excludes_soft_deleted_frames_from_player_totals(): void
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

        $player = User::factory()->create([
            'team_id' => $team->id,
            'name' => 'Conrad Wass',
        ]);

        $opponentPlayer = User::factory()->create([
            'team_id' => $opponent->id,
            'name' => 'Opponent Player',
        ]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 2,
            'away_team_id' => $opponent->id,
            'away_team_name' => $opponent->name,
            'away_score' => 1,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponentPlayer->id,
            'away_score' => 0,
        ]);

        $deletedFrame = Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponentPlayer->id,
            'away_score' => 0,
        ]);

        $deletedFrame->delete();

        $players = (new GetTeamPlayers($team, $section))();
        $playerStats = $players->firstWhere('id', $player->id);

        $this->assertNotNull($playerStats);
        $this->assertSame(1, (int) $playerStats->frames_played);
        $this->assertSame(1, (int) $playerStats->frames_won);
        $this->assertSame(0, (int) $playerStats->frames_lost);

    }

    public function test_team_players_section_paginates_five_players_without_page_params(): void
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

        $players = collect([
            'Aaron',
            'Barry',
            'Chris',
            'Darren',
            'Ethan',
            'Frank',
        ])->map(fn (string $name) => User::factory()->create([
            'team_id' => $team->id,
            'name' => $name,
        ]));

        Livewire::test(PlayersSection::class, ['team' => $team, 'section' => $section])
            ->assertSeeInOrder($players->take(5)->pluck('name')->all())
            ->assertDontSee($players->last()->name)
            ->call('nextPage')
            ->assertSee($players->last()->name)
            ->assertDontSee($players->first()->name)
            ->assertSee('Page 2');
    }

    public function test_team_fixtures_section_defaults_to_the_page_containing_the_current_week_and_paginates_by_five(): void
    {
        $season = Season::factory()->create([
            'is_open' => true,
            'dates' => collect(range(5, 1))
                ->map(fn (int $weeksAgo) => now()->subWeeks($weeksAgo)->toDateString())
                ->push(now()->toDateString())
                ->values()
                ->all(),
        ]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create(['name' => 'Blues']);
        $section->teams()->attach($team->id, ['sort' => 1]);

        $opponents = collect(range(1, 6))->map(function (int $index) use ($section, $season, $ruleset, $team) {
            $opponent = Team::factory()->create([
                'name' => 'Opponents '.$index,
            ]);

            $section->teams()->attach($opponent->id, ['sort' => $index + 1]);

            Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $team->id,
                'away_team_id' => $opponent->id,
                'week' => $index,
                'fixture_date' => now()->subWeeks(6 - $index),
            ]);

            return $opponent;
        });

        Livewire::test(FixturesSection::class, ['team' => $team, 'section' => $section])
            ->assertSet('page', 2)
            ->assertSee('Opponents 6')
            ->assertDontSee('Opponents 1')
            ->call('previousPage')
            ->assertSet('page', 1)
            ->assertSee('Opponents 1')
            ->assertDontSee('Opponents 6');
    }

    public function test_team_history_section_shows_finishing_position_and_paginates_by_five(): void
    {
        $currentSeason = Season::factory()->create([
            'is_open' => true,
            'dates' => [now()->toDateString()],
        ]);
        $historySeasons = collect(range(1, 6))->map(function (int $index) {
            return Season::factory()->create([
                'is_open' => false,
                'name' => '20'.(20 + $index).'/'.(21 + $index).' Season',
                'dates' => [now()->subYears($index)->toDateString()],
            ]);
        });

        $ruleset = Ruleset::factory()->create(['name' => 'International Rules']);
        $currentSection = Section::factory()->create([
            'season_id' => $currentSeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Current Division',
        ]);

        $team = Team::factory()->create(['name' => 'Blues']);
        $currentOpponent = Team::factory()->create(['name' => 'Current Opponent']);
        $currentSection->teams()->attach($team->id, ['sort' => 1]);
        $currentSection->teams()->attach($currentOpponent->id, ['sort' => 2]);

        foreach ($historySeasons as $index => $season) {
            $section = Section::factory()->create([
                'season_id' => $season->id,
                'ruleset_id' => $ruleset->id,
                'name' => 'Division '.($index + 1),
            ]);

            $opponent = Team::factory()->create(['name' => 'Opponent '.($index + 1)]);

            $section->teams()->attach($team->id, ['sort' => 1, 'deducted' => $index === 0 ? 2 : 0]);
            $section->teams()->attach($opponent->id, ['sort' => 2]);

            $fixture = Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $team->id,
                'away_team_id' => $opponent->id,
                'week' => 1,
                'fixture_date' => now()->subYears($index + 1),
            ]);

            Result::factory()->create([
                'fixture_id' => $fixture->id,
                'home_team_id' => $team->id,
                'home_team_name' => $team->name,
                'home_score' => 6,
                'away_team_id' => $opponent->id,
                'away_team_name' => $opponent->name,
                'away_score' => 4,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'is_confirmed' => true,
            ]);

            if ($index === 1) {
                Expulsion::query()->create([
                    'season_id' => $season->id,
                    'expellable_id' => $team->id,
                    'expellable_type' => Team::class,
                ]);
            }
        }

        Livewire::test(HistorySection::class, ['team' => $team, 'currentSection' => $currentSection])
            ->assertSee('Pos')
            ->assertSee('1')
            ->assertDontSee('-2 pts deducted')
            ->assertDontSee('Team expelled')
            ->assertDontSee($historySeasons->get(1)->name)
            ->assertSee('Page 1')
            ->assertDontSee($historySeasons->first()->name)
            ->call('nextPage')
            ->assertSee('Page 2')
            ->assertSee($historySeasons->first()->name)
            ->assertDontSee($historySeasons->last()->name)
            ->assertDontSee($historySeasons->get(1)->name);
    }

    public function test_team_profile_does_not_link_bye_fixture(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create(['name' => 'Blues']);
        $opponent = Team::factory()->create(['name' => Team::BYE_NAME]);

        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponent->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
        ]);

        $response = $this->get(route('team.show', $team));

        $response->assertOk();
        $response->assertDontSee('href="'.route('fixture.show', $fixture).'"', false);
    }

    public function test_bye_team_profile_returns_not_found(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create(['name' => Team::BYE_NAME]);
        $opponent = Team::factory()->create();

        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponent->id, ['sort' => 2]);

        $this->get(route('team.show', $team))
            ->assertNotFound();
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
            'home_score' => 6,
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
        $response->assertSeeText('6');
        $response->assertSeeText('4');
    }

    public function test_admin_sees_team_knockout_submission_link_on_public_team_profile(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create();
        $opponent = Team::factory()->create();
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

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

        $match = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 11,
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('team.show', $team))
            ->assertOk()
            ->assertSee(route('knockout.matches.submit', $match), false);
    }
}
