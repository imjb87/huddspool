<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Livewire\Knockout\Show as KnockoutShow;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KnockoutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_knockout_show_page_renders_the_livewire_round_pager_and_defaults_to_the_current_published_round(): void
    {
        $season = Season::factory()->create(['name' => '2026 Season']);

        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Summer Singles Cup',
            'type' => KnockoutType::Singles,
            'best_of' => 5,
        ]);

        $completedRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Quarter Final',
            'position' => 1,
            'scheduled_for' => now()->subWeek(),
            'is_visible' => true,
        ]);

        $currentRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Semi Final',
            'position' => 2,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $futureRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Championship Match',
            'position' => 3,
            'scheduled_for' => now()->addWeek(),
            'is_visible' => false,
        ]);

        $completedVenue = Venue::factory()->create(['name' => 'Riverside Club']);

        $completedHomePlayer = User::factory()->create(['name' => 'Alice Adams']);
        $completedAwayPlayer = User::factory()->create(['name' => 'Bryn Baker']);
        $currentHomePlayer = User::factory()->create(['name' => 'Cara Cole']);
        $currentAwayPlayer = User::factory()->create(['name' => 'Drew Dale']);

        $completedHomeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $completedHomePlayer->id,
        ]);

        $completedAwayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $completedAwayPlayer->id,
        ]);

        $currentHomeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $currentHomePlayer->id,
        ]);

        $currentAwayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $currentAwayPlayer->id,
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $completedRound->id,
            'position' => 1,
            'home_participant_id' => $completedHomeParticipant->id,
            'away_participant_id' => $completedAwayParticipant->id,
            'venue_id' => $completedVenue->id,
            'home_score' => 3,
            'away_score' => 1,
            'best_of' => 5,
            'completed_at' => now()->subDays(5),
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $currentRound->id,
            'position' => 1,
            'home_participant_id' => $currentHomeParticipant->id,
            'away_participant_id' => $currentAwayParticipant->id,
            'starts_at' => now()->setDate(2026, 4, 3),
            'best_of' => 5,
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $futureRound->id,
            'position' => 1,
            'home_participant_id' => $currentHomeParticipant->id,
            'away_participant_id' => $currentAwayParticipant->id,
            'best_of' => 5,
        ]);

        $response = $this->get(route('knockout.show', $knockout));

        $response->assertOk()
            ->assertSeeLivewire(KnockoutShow::class)
            ->assertSee('data-knockout-show-page', false)
            ->assertSee('data-knockout-round-shell', false)
            ->assertSee('grid gap-8 lg:grid-cols-3 lg:gap-10', false)
            ->assertSee('divide-y divide-gray-200 dark:divide-zinc-800/80', false)
            ->assertSee('data-knockout-round-controls', false)
            ->assertSee('data-knockout-round-skeleton', false)
            ->assertSee('dark:bg-zinc-900', false)
            ->assertSee('dark:border-zinc-800/80', false)
            ->assertSee('dark:text-gray-100', false)
            ->assertSeeText('Summer Singles Cup')
            ->assertSeeText('Semi Final')
            ->assertSeeText('3 Apr')
            ->assertSee('href="'.route('player.show', $currentHomePlayer).'"', false)
            ->assertDontSeeText('Quarter Final')
            ->assertDontSeeText('Riverside Club')
            ->assertDontSeeText('Championship Match');
    }

    public function test_knockout_round_pager_can_navigate_between_published_rounds_and_hides_future_rounds(): void
    {
        $season = Season::factory()->create();

        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Knockout Ladder',
            'type' => KnockoutType::Singles,
            'best_of' => 5,
        ]);

        $firstRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 1',
            'position' => 1,
            'scheduled_for' => now()->subWeeks(2),
            'is_visible' => true,
        ]);

        $secondRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 2',
            'position' => 2,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 3',
            'position' => 3,
            'scheduled_for' => now()->addDay(),
            'is_visible' => false,
        ]);

        $firstRoundHomePlayer = User::factory()->create(['name' => 'First Round Home']);
        $firstRoundAwayPlayer = User::factory()->create(['name' => 'First Round Away']);
        $secondRoundHomePlayer = User::factory()->create(['name' => 'Second Round Home']);
        $secondRoundAwayPlayer = User::factory()->create(['name' => 'Second Round Away']);

        $firstRoundHomeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $firstRoundHomePlayer->id,
        ]);

        $firstRoundAwayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $firstRoundAwayPlayer->id,
        ]);

        $secondRoundHomeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $secondRoundHomePlayer->id,
        ]);

        $secondRoundAwayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $secondRoundAwayPlayer->id,
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $firstRound->id,
            'position' => 1,
            'home_participant_id' => $firstRoundHomeParticipant->id,
            'away_participant_id' => $firstRoundAwayParticipant->id,
            'home_score' => 3,
            'away_score' => 0,
            'best_of' => 5,
            'completed_at' => now()->subWeek(),
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $secondRound->id,
            'position' => 1,
            'home_participant_id' => $secondRoundHomeParticipant->id,
            'away_participant_id' => $secondRoundAwayParticipant->id,
            'best_of' => 5,
        ]);

        Livewire::test(KnockoutShow::class, ['knockout' => $knockout])
            ->assertSet('currentRoundId', $secondRound->id)
            ->assertSee('Round 2')
            ->assertSee('Second Round Home')
            ->assertDontSee('Round 1')
            ->assertDontSee('Round 3')
            ->call('previousRound')
            ->assertSet('currentRoundId', $firstRound->id)
            ->assertSee('Round 1')
            ->assertSee('First Round Home')
            ->assertDontSee('Round 2')
            ->call('nextRound')
            ->assertSet('currentRoundId', $secondRound->id)
            ->assertSee('Round 2');
    }

    public function test_knockout_show_page_keeps_team_links_and_empty_round_state_for_the_active_round(): void
    {
        $season = Season::factory()->create();

        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Team Cup',
            'type' => KnockoutType::Team,
            'best_of' => 11,
        ]);

        $firstRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 1',
            'position' => 1,
            'scheduled_for' => now()->subWeek(),
            'is_visible' => true,
        ]);

        $currentRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 2',
            'position' => 2,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $homeTeam = Team::factory()->create(['name' => 'North Stars']);
        $awayTeam = Team::factory()->create(['name' => 'South Town']);

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'team_id' => $homeTeam->id,
        ]);

        $awayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'team_id' => $awayTeam->id,
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $firstRound->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'home_score' => 6,
            'away_score' => 2,
            'best_of' => 11,
            'completed_at' => now()->subDays(3),
        ]);

        Livewire::test(KnockoutShow::class, ['knockout' => $knockout])
            ->assertSet('currentRoundId', $currentRound->id)
            ->assertSee('Round 2')
            ->assertSee('No matches scheduled for this round yet.')
            ->assertDontSee('North Stars');

        Livewire::test(KnockoutShow::class, ['knockout' => $knockout])
            ->call('previousRound')
            ->assertSee('href="'.route('team.show', $homeTeam).'"', false)
            ->assertSee('href="'.route('team.show', $awayTeam).'"', false);
    }

    public function test_knockout_show_page_uses_ampersands_for_doubles_pair_names(): void
    {
        $season = Season::factory()->create();

        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Doubles Cup',
            'type' => KnockoutType::Doubles,
            'best_of' => 7,
        ]);

        $round = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 1',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $homePlayerOne = User::factory()->create(['name' => 'Amy A']);
        $homePlayerTwo = User::factory()->create(['name' => 'Beth B']);
        $awayPlayerOne = User::factory()->create(['name' => 'Cara C']);
        $awayPlayerTwo = User::factory()->create(['name' => 'Dana D']);

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $homePlayerOne->id,
            'player_two_id' => $homePlayerTwo->id,
        ]);

        $awayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $awayPlayerOne->id,
            'player_two_id' => $awayPlayerTwo->id,
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 7,
        ]);

        $response = $this->get(route('knockout.show', $knockout));

        $response->assertOk()
            ->assertSeeText('Amy A')
            ->assertSeeText('Beth B')
            ->assertSeeText('Cara C')
            ->assertSeeText('Dana D')
            ->assertSee('&amp;', false)
            ->assertDontSee(' / ', false);
    }
}
