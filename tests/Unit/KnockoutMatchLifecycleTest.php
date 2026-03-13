<?php

namespace Tests\Unit;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KnockoutMatchLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_participant_match_is_auto_completed_as_a_bye(): void
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Singles, 5);

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Only Player',
        ]);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'best_of' => 5,
        ]);

        $match->refresh();

        $this->assertSame($homeParticipant->id, $match->winner_participant_id);
        $this->assertNotNull($match->completed_at);
    }

    public function test_forfeit_sets_winner_clears_scores_and_records_reporter_metadata(): void
    {
        Carbon::setTestNow('2026-03-13 20:15:00');

        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Doubles, 7);

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Home Pair',
        ]);

        $awayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Away Pair',
        ]);

        $reporter = User::factory()->create();

        $this->actingAs($reporter);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'forfeit_participant_id' => $homeParticipant->id,
            'best_of' => 7,
        ]);

        $match->refresh();

        $this->assertSame($awayParticipant->id, $match->winner_participant_id);
        $this->assertNull($match->home_score);
        $this->assertNull($match->away_score);
        $this->assertSame($reporter->id, $match->reported_by_id);
        $this->assertTrue($match->reported_at->equalTo(Carbon::now()));
        $this->assertSame('Updated in admin.', $match->report_reason);
        $this->assertNotNull($match->completed_at);

        Carbon::setTestNow();
    }

    public function test_team_knockout_rejects_an_away_team_venue_assignment(): void
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Team, 11, 'Quarter Final');
        ['homeParticipant' => $homeParticipant, 'awayParticipant' => $awayParticipant, 'awayVenue' => $awayVenue] = $this->createTeamParticipants($knockout);

        $this->expectException(ValidationException::class);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'venue_id' => $awayVenue->id,
            'best_of' => 11,
        ]);
    }

    public function test_team_knockout_allows_the_home_team_venue_for_rounds_without_semi_or_final_in_the_name(): void
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Team, 11, 'Round 1');
        ['homeParticipant' => $homeParticipant, 'awayParticipant' => $awayParticipant, 'homeVenue' => $homeVenue] = $this->createTeamParticipants($knockout);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 11,
        ]);

        $match->load('round.knockout', 'homeParticipant.team', 'awayParticipant.team');
        $match->update([
            'venue_id' => $homeVenue->id,
        ]);

        $this->assertSame($homeVenue->id, $match->venue_id);
    }

    public function test_winner_is_synced_to_the_next_match_slot_and_removed_when_result_is_cleared(): void
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Singles, 5, 'Quarter Final');

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Home',
        ]);

        $awayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Away',
        ]);

        $nextRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 2',
            'position' => 2,
            'is_visible' => true,
        ]);

        $nextMatch = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $nextRound->id,
            'position' => 2,
            'best_of' => 5,
        ]);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'home_score' => 3,
            'away_score' => 1,
            'best_of' => 5,
            'next_match_id' => $nextMatch->id,
            'next_slot' => 'home',
        ]);

        $this->assertSame($homeParticipant->id, $nextMatch->fresh()->home_participant_id);

        $match->clearResult();

        $this->assertNull($nextMatch->fresh()->home_participant_id);
    }

    public function test_team_winner_filling_the_away_slot_sets_the_next_match_venue_from_the_existing_home_participant(): void
    {
        ['knockout' => $knockout, 'round' => $round] = $this->createKnockoutContext(KnockoutType::Team, 11, 'Quarter Final');
        ['homeParticipant' => $homeParticipant, 'awayParticipant' => $awayParticipant] = $this->createTeamParticipants($knockout);
        ['homeParticipant' => $nextHomeParticipant, 'homeVenue' => $nextHomeVenue] = $this->createTeamParticipants($knockout);

        $nextRound = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Round 2',
            'position' => 2,
            'is_visible' => true,
        ]);

        $nextMatch = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $nextRound->id,
            'position' => 2,
            'home_participant_id' => $nextHomeParticipant->id,
            'best_of' => 11,
        ]);

        KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'home_score' => 6,
            'away_score' => 4,
            'best_of' => 11,
            'next_match_id' => $nextMatch->id,
            'next_slot' => 'away',
        ]);

        $nextMatch->refresh();

        $this->assertSame($homeParticipant->id, $nextMatch->away_participant_id);
        $this->assertSame($nextHomeVenue->id, $nextMatch->venue_id);
    }

    /**
     * @return array{knockout: Knockout, round: KnockoutRound}
     */
    private function createKnockoutContext(KnockoutType $type, ?int $bestOf = null, string $roundName = 'Quarter Final'): array
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
            'name' => $roundName,
            'position' => 1,
            'is_visible' => true,
        ]);

        return compact('knockout', 'round');
    }

    /**
     * @return array{
     *     homeParticipant: KnockoutParticipant,
     *     awayParticipant: KnockoutParticipant,
     *     homeVenue: Venue,
     *     awayVenue: Venue
     * }
     */
    private function createTeamParticipants(Knockout $knockout): array
    {
        config([
            'services.nominatim.search_url' => 'https://example.com/search',
            'services.nominatim.user_agent' => 'Configured Geocoder',
        ]);

        Http::fake([
            'https://example.com/search*' => Http::response([
                ['lat' => '53.6458', 'lon' => '-1.7850'],
            ]),
        ]);

        $homeVenue = Venue::factory()->create([
            'address' => 'Home Venue, HD1 1AA',
        ]);
        $awayVenue = Venue::factory()->create([
            'address' => 'Away Venue, HD2 2BB',
        ]);

        $homeTeam = Team::factory()->create([
            'venue_id' => $homeVenue->id,
        ]);
        $awayTeam = Team::factory()->create([
            'venue_id' => $awayVenue->id,
        ]);

        $homeParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'team_id' => $homeTeam->id,
        ]);

        $awayParticipant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'team_id' => $awayTeam->id,
        ]);

        return compact('homeParticipant', 'awayParticipant', 'homeVenue', 'awayVenue');
    }
}
