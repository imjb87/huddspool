<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Livewire\Player\FramesSection;
use App\Livewire\Player\HistorySection;
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
use App\Queries\GetPlayerSeasonHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class PlayerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_profile_displays_averages_and_recent_frames(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $archivedSeason = Season::factory()->create(['is_open' => false]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $archivedSection = Section::factory()->create([
            'season_id' => $archivedSeason->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();

        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponentTeam->id, ['sort' => 2]);
        $archivedSection->teams()->attach($team->id, ['sort' => 1, 'deducted' => 2]);
        $archivedSection->teams()->attach($opponentTeam->id, ['sort' => 2]);

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

        $archivedFixture = Fixture::factory()->create([
            'season_id' => $archivedSeason->id,
            'section_id' => $archivedSection->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
        ]);

        $archivedResult = Result::factory()->create([
            'fixture_id' => $archivedFixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 7,
            'away_team_id' => $opponentTeam->id,
            'away_team_name' => $opponentTeam->name,
            'away_score' => 3,
            'section_id' => $archivedSection->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
        ]);

        Frame::create([
            'result_id' => $archivedResult->id,
            'home_player_id' => $player->id,
            'home_score' => 2,
            'away_player_id' => $opponent->id,
            'away_score' => 1,
        ]);

        Expulsion::query()->create([
            'season_id' => $archivedSeason->id,
            'expellable_id' => $player->id,
            'expellable_type' => User::class,
        ]);

        $this->actingAs($player);

        $response = $this->get(route('player.show', $player));

        $response->assertStatus(200);
        $response->assertSee('data-player-page', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-section-shared-header', false);
        $response->assertSee('data-player-profile-section', false);
        $response->assertSee('data-player-frames-section', false);
        $response->assertSee('data-player-history-section', false);
        $response->assertSeeLivewire(HistorySection::class);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('dark:bg-zinc-900', false);
        $response->assertSee('dark:border-zinc-800/80', false);
        $response->assertSee('dark:text-gray-100', false);
        $response->assertSeeText($player->name);
        $response->assertSeeText('Player');
        $response->assertSeeText($team->name);
        $response->assertSeeTextInOrder(['Played', 'Won', 'Lost']);
        $response->assertSeeText('50%');
        $response->assertSeeText($opponent->name);
        $response->assertSeeTextInOrder(['Frames', $opponentTeam->name]);
        $response->assertDontSeeText('-2 pts deducted');
        $response->assertDontSeeText('Player expelled');
        $response->assertDontSeeText($archivedSeason->name);
        $response->assertSee(
            route('history.section.show', [
                'season' => $archivedSeason->slug,
                'ruleset' => $ruleset->slug,
                'section' => $archivedSection->slug,
            ]),
            false,
        );
    }

    public function test_player_profile_shows_read_only_knockout_links(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $team = Team::factory()->create();
        $player = User::factory()->create(['team_id' => $team->id]);
        $opponent = User::factory()->create();

        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Singles KO',
            'type' => KnockoutType::Singles,
        ]);

        $round = KnockoutRound::query()->create([
            'knockout_id' => $knockout->id,
            'name' => 'Quarter-finals',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $player->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $opponent->id,
        ]);

        $completedMatch = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'home_score' => 4,
            'away_score' => 2,
            'best_of' => 7,
            'starts_at' => now()->subDays(2),
        ]);

        $teamKnockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Team KO',
            'type' => KnockoutType::Team,
        ]);

        $teamRound = KnockoutRound::query()->create([
            'knockout_id' => $teamKnockout->id,
            'name' => 'Semi-finals',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $teamHomeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $team->id,
        ]);

        $teamAwayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => Team::factory()->create()->id,
        ]);

        KnockoutMatch::query()->create([
            'knockout_id' => $teamKnockout->id,
            'knockout_round_id' => $teamRound->id,
            'position' => 1,
            'home_participant_id' => $teamHomeParticipant->id,
            'away_participant_id' => $teamAwayParticipant->id,
            'best_of' => 11,
            'starts_at' => now()->subDay(),
        ]);

        $response = $this->get(route('player.show', $player))
            ->assertOk()
            ->assertSee('data-player-knockout-section', false)
            ->assertSeeText('Knockouts')
            ->assertSeeText($knockout->name)
            ->assertSee(route('knockout.show', $knockout), false)
            ->assertDontSee(route('knockout.matches.submit', $completedMatch), false)
            ->assertSeeText('4')
            ->assertSeeText('2');

        preg_match('/<section[^>]*data-player-knockout-section[^>]*>.*?<\/section>/s', $response->getContent(), $matches);

        $knockoutSection = $matches[0] ?? '';

        $this->assertNotSame('', $knockoutSection);
        $this->assertStringContainsString($knockout->name, $knockoutSection);
        $this->assertStringNotContainsString($teamKnockout->name, $knockoutSection);
    }

    public function test_admin_sees_knockout_submission_link_on_public_player_profile(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $team = Team::factory()->create();
        $player = User::factory()->create(['team_id' => $team->id]);
        $opponent = User::factory()->create();
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Singles KO',
            'type' => KnockoutType::Singles,
        ]);

        $round = KnockoutRound::query()->create([
            'knockout_id' => $knockout->id,
            'name' => 'Quarter-finals',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $player->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $opponent->id,
        ]);

        $match = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 7,
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('player.show', $player))
            ->assertOk()
            ->assertSee(route('knockout.matches.submit', $match), false);
    }

    public function test_guest_does_not_see_private_contact_details_on_player_profile(): void
    {
        $team = Team::factory()->create();
        $player = User::factory()->create([
            'team_id' => $team->id,
            'email' => 'conrad@example.com',
            'telephone' => '07123 456789',
        ]);

        $this->get(route('player.show', $player))
            ->assertOk()
            ->assertDontSeeText('Email address')
            ->assertDontSeeText('Phone number')
            ->assertDontSeeText('conrad@example.com')
            ->assertDontSeeText('07123 456789')
            ->assertDontSee('mailto:', false)
            ->assertDontSee('tel:', false);
    }

    public function test_player_profile_limits_recent_frames_to_twenty_entries(): void
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
        foreach (range(1, 21) as $index) {
            $fixture = Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $team->id,
                'away_team_id' => $opponentTeam->id,
                'fixture_date' => now()->subDays(21 - $index),
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

            $opponent = User::factory()->create([
                'name' => sprintf('Opponent %02d', $index),
                'team_id' => $opponentTeam->id,
            ]);

            Frame::create([
                'result_id' => $result->id,
                'home_player_id' => $player->id,
                'home_score' => 1,
                'away_player_id' => $opponent->id,
                'away_score' => 0,
            ]);
        }

        $response = $this->actingAs($player)->get(route('player.show', $player));

        $response->assertOk();
        $response->assertSee('data-player-frames-controls', false);
        $response->assertSee('ui-button-primary', false);
        $response->assertSeeText('Page 1');
        $response->assertDontSeeText('Opponent 01');
        $response->assertDontSeeText('Opponent 16');
        $response->assertSeeText('Opponent 17');
        $response->assertSeeText('Opponent 21');

        Livewire::test(FramesSection::class, [
            'player' => $player,
            'section' => $section,
        ])
            ->assertSeeText('Page 1')
            ->assertDontSeeText('Opponent 16')
            ->call('nextPage')
            ->assertSeeText('Page 2')
            ->assertSeeText('Opponent 16')
            ->assertDontSeeText('Opponent 17')
            ->assertDontSeeText('Opponent 11');
    }

    public function test_player_season_history_excludes_open_seasons_and_keeps_loss_totals_correct(): void
    {
        $openSeason = Season::factory()->create(['is_open' => true]);
        $archivedSeason = Season::factory()->create(['is_open' => false]);
        $ruleset = Ruleset::factory()->create();
        $openSection = Section::factory()->create([
            'season_id' => $openSeason->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $archivedSection = Section::factory()->create([
            'season_id' => $archivedSeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Archived Premier',
        ]);

        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();

        $openSection->teams()->attach($team->id, ['sort' => 1]);
        $openSection->teams()->attach($opponentTeam->id, ['sort' => 2]);
        $archivedSection->teams()->attach($team->id, ['sort' => 1]);
        $archivedSection->teams()->attach($opponentTeam->id, ['sort' => 2]);

        $player = User::factory()->create(['team_id' => $team->id]);
        $opponent = User::factory()->create(['team_id' => $opponentTeam->id]);

        $openFixture = Fixture::factory()->create([
            'season_id' => $openSeason->id,
            'section_id' => $openSection->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
        ]);
        $archivedFixture = Fixture::factory()->create([
            'season_id' => $archivedSeason->id,
            'section_id' => $archivedSection->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
        ]);

        $openResult = Result::factory()->create([
            'fixture_id' => $openFixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 1,
            'away_team_id' => $opponentTeam->id,
            'away_team_name' => $opponentTeam->name,
            'away_score' => 0,
            'section_id' => $openSection->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
        ]);
        $archivedResult = Result::factory()->create([
            'fixture_id' => $archivedFixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 1,
            'away_team_id' => $opponentTeam->id,
            'away_team_name' => $opponentTeam->name,
            'away_score' => 1,
            'section_id' => $archivedSection->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
        ]);

        Frame::create([
            'result_id' => $openResult->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);
        Frame::create([
            'result_id' => $archivedResult->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);
        Frame::create([
            'result_id' => $archivedResult->id,
            'home_player_id' => $player->id,
            'home_score' => 0,
            'away_player_id' => $opponent->id,
            'away_score' => 1,
        ]);
        Frame::create([
            'result_id' => $archivedResult->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponent->id,
            'away_score' => 1,
        ]);

        Cache::flush();

        $history = (new GetPlayerSeasonHistory($player))();

        $this->assertCount(1, $history);
        $this->assertSame($archivedSeason->id, $history->first()['season_id']);
        $this->assertSame(3, $history->first()['played']);
        $this->assertSame(1, $history->first()['wins']);
        $this->assertSame(1, $history->first()['draws']);
        $this->assertSame(1, $history->first()['losses']);
        $this->assertSame(33.3, $history->first()['win_percentage']);
        $this->assertSame(33.3, $history->first()['loss_percentage']);
    }

    public function test_player_history_is_paginated_in_place_with_a_shared_header_row(): void
    {
        $archivedSeasons = Season::factory()->count(6)->create(['is_open' => false]);
        $ruleset = Ruleset::factory()->create();
        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();
        $player = User::factory()->create(['team_id' => $team->id]);
        $opponents = User::factory()->count(6)->create(['team_id' => $opponentTeam->id]);

        foreach ($archivedSeasons as $index => $season) {
            $section = Section::factory()->create([
                'season_id' => $season->id,
                'ruleset_id' => $ruleset->id,
                'name' => "Section {$index}",
            ]);

            $section->teams()->attach($team->id, ['sort' => 1]);
            $section->teams()->attach($opponentTeam->id, ['sort' => 2]);

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
                'home_score' => 5,
                'away_team_id' => $opponentTeam->id,
                'away_team_name' => $opponentTeam->name,
                'away_score' => 3,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'submitted_by' => $player->id,
            ]);

            Frame::create([
                'result_id' => $result->id,
                'home_player_id' => $player->id,
                'home_score' => 1,
                'away_player_id' => $opponents[$index]->id,
                'away_score' => 0,
            ]);
        }

        $response = $this->get(route('player.show', $player));

        $response->assertOk();
        $response->assertSeeLivewire(HistorySection::class);
        $response->assertSee('data-player-history-controls', false);

        preg_match('/<section[^>]*data-player-history-section[^>]*>.*?<\/section>/s', $response->getContent(), $matches);

        $historySection = $matches[0] ?? '';

        $this->assertStringContainsString('Played', $historySection);
        $this->assertSame(1, substr_count($historySection, 'Played'));
        $this->assertSame(1, substr_count($historySection, 'Won'));
        $this->assertSame(1, substr_count($historySection, 'Lost'));

        Livewire::test(HistorySection::class, ['player' => $player])
            ->assertSee('Page 1')
            ->assertSee('100%')
            ->assertSee($archivedSeasons->last()->name)
            ->assertDontSee($archivedSeasons->first()->name)
            ->call('nextPage')
            ->assertSee('Page 2')
            ->assertSee($archivedSeasons->first()->name)
            ->assertDontSee($archivedSeasons->last()->name);
    }
}
