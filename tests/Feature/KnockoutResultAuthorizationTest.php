<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Livewire\Knockout\SubmitResult;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KnockoutResultAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_knockout_submission_route(): void
    {
        ['match' => $match] = $this->createSinglesMatchContext();

        $this->get(route('knockout.matches.submit', $match))
            ->assertRedirect(route('login'));
    }

    public function test_doubles_second_player_can_open_knockout_submission_route(): void
    {
        ['match' => $match, 'homePlayerTwo' => $homePlayerTwo] = $this->createDoublesMatchContext();

        $this->actingAs($homePlayerTwo)
            ->get(route('knockout.matches.submit', $match))
            ->assertOk()
            ->assertSee('data-knockout-submit-page', false)
            ->assertSee('data-knockout-submit-header', false)
            ->assertSee('data-knockout-submit-context', false)
            ->assertSeeText('Match details')
            ->assertSeeText('Match score')
            ->assertSee('data-knockout-submit-form', false)
            ->assertSeeLivewire(SubmitResult::class);
    }

    public function test_unrelated_player_receives_forbidden_for_singles_knockout_submission_route(): void
    {
        ['match' => $match] = $this->createSinglesMatchContext();

        $unrelatedUser = User::factory()->create();

        $this->actingAs($unrelatedUser)
            ->get(route('knockout.matches.submit', $match))
            ->assertForbidden();
    }

    public function test_team_captain_can_open_team_knockout_submission_route(): void
    {
        ['match' => $match, 'homeTeam' => $homeTeam] = $this->createTeamMatchContext();

        $captain = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $homeTeam->update(['captain_id' => $captain->id]);

        $this->actingAs($captain)
            ->get(route('knockout.matches.submit', $match))
            ->assertOk()
            ->assertSee('data-knockout-submit-shell', false)
            ->assertSeeLivewire(SubmitResult::class);
    }

    public function test_team_admin_can_open_team_knockout_submission_route(): void
    {
        ['match' => $match, 'homeTeam' => $homeTeam] = $this->createTeamMatchContext();

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $this->actingAs($teamAdmin)
            ->get(route('knockout.matches.submit', $match))
            ->assertOk()
            ->assertSeeLivewire(SubmitResult::class);
    }

    public function test_regular_team_member_receives_forbidden_for_team_knockout_submission_route(): void
    {
        ['match' => $match, 'homeTeam' => $homeTeam] = $this->createTeamMatchContext();

        $player = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $this->actingAs($player)
            ->get(route('knockout.matches.submit', $match))
            ->assertForbidden();
    }

    public function test_completed_knockout_match_is_forbidden_on_route_but_component_can_mount_for_authorized_user(): void
    {
        ['match' => $match, 'homePlayer' => $homePlayer] = $this->createSinglesMatchContext();

        $match->recordResult(3, 1, $homePlayer);
        $match->refresh();

        $this->actingAs($homePlayer)
            ->get(route('knockout.matches.submit', $match))
            ->assertForbidden();

        Livewire::actingAs($homePlayer)
            ->test(SubmitResult::class, ['match' => $match])
            ->assertSet('homeScore', 3)
            ->assertSet('awayScore', 1);
    }

    public function test_team_captain_sees_submit_link_on_player_profile(): void
    {
        ['match' => $match, 'homeTeam' => $homeTeam] = $this->createTeamMatchContext();

        $captain = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $homeTeam->update(['captain_id' => $captain->id]);
        $match->update(['starts_at' => now()->addDay()]);

        $this->actingAs($captain)
            ->get(route('player.show', $captain))
            ->assertOk()
            ->assertSeeText('Submit result');
    }

    public function test_regular_team_member_does_not_see_submit_link_on_player_profile(): void
    {
        ['match' => $match, 'homeTeam' => $homeTeam] = $this->createTeamMatchContext();

        $player = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $match->update(['starts_at' => now()->addDay()]);

        $this->actingAs($player)
            ->get(route('player.show', $player))
            ->assertOk()
            ->assertDontSeeText('Submit result');
    }

    /**
     * @return array{match: KnockoutMatch, homePlayer: User, awayPlayer: User}
     */
    private function createSinglesMatchContext(): array
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Singles, 5);

        $homePlayer = User::factory()->create();
        $awayPlayer = User::factory()->create();

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $homePlayer->id,
        ]);

        $awayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $awayPlayer->id,
        ]);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 5,
        ]);

        return compact('match', 'homePlayer', 'awayPlayer');
    }

    /**
     * @return array{match: KnockoutMatch, homePlayerTwo: User}
     */
    private function createDoublesMatchContext(): array
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Doubles, 7);

        $homePlayerOne = User::factory()->create();
        $homePlayerTwo = User::factory()->create();
        $awayPlayerOne = User::factory()->create();
        $awayPlayerTwo = User::factory()->create();

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

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 7,
        ]);

        return compact('match', 'homePlayerTwo');
    }

    /**
     * @return array{match: KnockoutMatch, homeTeam: Team, awayTeam: Team}
     */
    private function createTeamMatchContext(): array
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Team);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'team_id' => $homeTeam->id,
        ]);

        $awayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'team_id' => $awayTeam->id,
        ]);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 11,
        ]);

        return compact('match', 'homeTeam', 'awayTeam');
    }

    /**
     * @return array{knockout: Knockout, round: KnockoutRound}
     */
    private function createKnockoutContext(KnockoutType $type, ?int $bestOf = null): array
    {
        $season = Season::factory()->create();

        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => "{$type->value} knockout",
            'type' => $type,
            'best_of' => $bestOf,
        ]);

        $round = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Quarter Final',
            'position' => 1,
            'is_visible' => true,
        ]);

        return compact('knockout', 'round');
    }
}
