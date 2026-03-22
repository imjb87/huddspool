<?php

namespace Tests\Feature;

use App\KnockoutType;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $archivedSection->teams()->attach($team->id, ['sort' => 1]);
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

        $this->actingAs($player);

        $response = $this->get(route('player.show', $player));

        $response->assertStatus(200);
        $response->assertSee('data-player-page', false);
        $response->assertSee('data-player-profile-section', false);
        $response->assertSee('data-player-frames-section', false);
        $response->assertSee('data-player-history-section', false);
        $response->assertSee('dark:bg-zinc-900', false);
        $response->assertSee('dark:border-zinc-800/80', false);
        $response->assertSee('dark:text-gray-100', false);
        $response->assertSeeText($player->name);
        $response->assertSeeText($team->name);
        $response->assertSeeTextInOrder(['Played', 'Won', 'Lost']);
        $response->assertSeeText('50%');
        $response->assertSeeText($opponent->name);
        $response->assertSeeTextInOrder(['Frames', $opponentTeam->name]);
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

    public function test_player_profile_stacks_doubles_knockout_names_across_two_lines(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $team = Team::factory()->create();
        $player = User::factory()->create([
            'team_id' => $team->id,
            'name' => 'John Corley',
        ]);
        $partner = User::factory()->create([
            'name' => 'Amanda Rose',
        ]);
        $opponentOne = User::factory()->create([
            'name' => 'Chris Heywood',
        ]);
        $opponentTwo = User::factory()->create([
            'name' => 'Carol Hey',
        ]);

        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Doubles KO',
            'type' => KnockoutType::Doubles,
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
            'player_two_id' => $partner->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $opponentOne->id,
            'player_two_id' => $opponentTwo->id,
        ]);

        KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 7,
            'starts_at' => now()->subDay(),
        ]);

        $response = $this->get(route('player.show', $player))->assertOk();

        preg_match('/<section[^>]*data-player-knockout-section[^>]*>.*?<\/section>/s', $response->getContent(), $matches);

        $knockoutSection = $matches[0] ?? '';

        $this->assertNotSame('', $knockoutSection);
        $this->assertStringContainsString('John Corley &amp; Amanda Rose', $knockoutSection);
        $this->assertStringContainsString('Chris Heywood &amp; Carol Hey', $knockoutSection);
        $this->assertMatchesRegularExpression('/John Corley &amp; Amanda Rose.*vs<\/span>.*Chris Heywood &amp; Carol Hey/s', $knockoutSection);
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
}
