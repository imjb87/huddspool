<?php

namespace Tests\Unit;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\Season;
use App\Services\KnockoutBracketBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KnockoutBracketBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_three_singles_players_create_a_match_and_a_final_for_the_winner_to_face_the_bye(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        $participants = collect(range(1, 3))->map(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        (new KnockoutBracketBuilder($knockout))->generate();

        $rounds = $knockout->rounds()->with('matches')->get();
        $firstRoundMatches = $rounds->first()->matches;
        $final = $rounds->last()->matches->first();
        $regularMatch = $firstRoundMatches->first(fn (KnockoutMatch $match): bool => $match->away_participant_id !== null);
        $byeMatch = $firstRoundMatches->first(fn (KnockoutMatch $match): bool => $match->away_participant_id === null);

        $this->assertCount(2, $firstRoundMatches);
        $this->assertSame($participants->first()->id, $byeMatch->home_participant_id);
        $this->assertSame($final->id, $regularMatch->next_match_id);
        $this->assertSame($final->id, $byeMatch->next_match_id);
        $this->assertSame($byeMatch->home_participant_id, $final->away_participant_id);
        $this->assertSame('Final', $final->round->name);
    }

    public function test_generated_matches_default_to_815_pm(): void
    {
        Carbon::setTestNow('2026-07-20 12:00:00');

        $knockout = $this->createKnockout(KnockoutType::Singles);

        collect(range(1, 4))->each(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        (new KnockoutBracketBuilder($knockout))->generate();

        $this->assertTrue(
            $knockout->matches()->get()->every(fn (KnockoutMatch $match): bool => $match->starts_at?->format('Y-m-d H:i:s') === '2026-07-20 20:15:00')
        );

        Carbon::setTestNow();
    }

    public function test_completed_round_can_be_redrawn_before_the_next_round_starts(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        $participants = collect(range(1, 8))->map(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        (new KnockoutBracketBuilder($knockout))->generate();

        $firstRoundMatches = $knockout->rounds()->orderBy('position')->first()->matches;

        foreach ($firstRoundMatches as $match) {
            $match->update([
                'home_score' => 3,
                'away_score' => 0,
            ]);
        }

        $secondRound = $knockout->rounds()->orderBy('position')->skip(1)->first();
        $winners = $firstRoundMatches->fresh()->pluck('winner_participant_id')->filter()->values();

        (new KnockoutBracketBuilder($knockout))->randomizeNextRound();

        $secondRoundMatches = $secondRound->matches()->get();
        $redrawnParticipants = $secondRoundMatches
            ->flatMap(fn (KnockoutMatch $match) => [$match->home_participant_id, $match->away_participant_id])
            ->filter()
            ->values();

        $this->assertEqualsCanonicalizing($winners->all(), $redrawnParticipants->all());
        $this->assertCount($winners->count(), $secondRoundMatches->flatMap(fn (KnockoutMatch $match) => $match->previousMatches));
        $this->assertTrue($firstRoundMatches->fresh()->every(fn (KnockoutMatch $match): bool => $match->next_match_id !== null));
    }

    public function test_an_incomplete_first_round_without_results_can_be_redrawn(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        $participants = collect(range(1, 8))->map(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        (new KnockoutBracketBuilder($knockout))->generate();
        $firstRound = $knockout->rounds()->orderBy('position')->first();

        $redrawnRound = (new KnockoutBracketBuilder($knockout))->randomizeRound($firstRound);
        $redrawnParticipants = $redrawnRound->matches
            ->flatMap(fn (KnockoutMatch $match) => [$match->home_participant_id, $match->away_participant_id])
            ->filter()
            ->values();

        $this->assertEqualsCanonicalizing($participants->pluck('id')->all(), $redrawnParticipants->all());
        $this->assertTrue($redrawnRound->matches->every(fn (KnockoutMatch $match): bool => $match->home_score === null
            && $match->away_score === null
            && $match->forfeit_participant_id === null));
    }

    public function test_a_round_with_a_recorded_result_cannot_be_redrawn(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        collect(range(1, 8))->each(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        (new KnockoutBracketBuilder($knockout))->generate();
        $firstRound = $knockout->rounds()->orderBy('position')->first();
        $firstRound->matches()->first()->update([
            'home_score' => 3,
            'away_score' => 0,
        ]);

        $this->expectException(ValidationException::class);

        (new KnockoutBracketBuilder($knockout))->randomizeRound($firstRound);
    }

    public function test_an_incomplete_later_round_can_be_redrawn_with_unresolved_feeders(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        collect(range(1, 8))->each(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        (new KnockoutBracketBuilder($knockout))->generate();
        $firstRound = $knockout->rounds()->orderBy('position')->first();
        $secondRound = $knockout->rounds()->orderBy('position')->skip(1)->first();
        $firstMatch = $firstRound->matches()->first();
        $firstMatch->update([
            'home_score' => 3,
            'away_score' => 0,
        ]);

        (new KnockoutBracketBuilder($knockout))->randomizeRound($secondRound);

        $knownWinnerId = $firstMatch->fresh()->winner_participant_id;
        $redrawnParticipants = $secondRound->fresh('matches')->matches
            ->flatMap(fn (KnockoutMatch $match) => [$match->home_participant_id, $match->away_participant_id])
            ->filter()
            ->values();

        $this->assertContains($knownWinnerId, $redrawnParticipants->all());
        $this->assertTrue($firstRound->matches()->get()->every(fn (KnockoutMatch $match): bool => $match->next_match_id !== null));
    }

    public function test_completed_round_can_be_redrawn_for_doubles_and_team_knockouts(): void
    {
        foreach ([KnockoutType::Doubles, KnockoutType::Team] as $type) {
            $knockout = $this->createKnockout($type);

            collect(range(1, 4))->each(fn (int $number) => KnockoutParticipant::create([
                'knockout_id' => $knockout->id,
                'label' => "{$type->value} {$number}",
            ]));

            (new KnockoutBracketBuilder($knockout))->generate();
            $firstRoundMatches = $knockout->rounds()->orderBy('position')->first()->matches;
            $winningScore = $type === KnockoutType::Team ? 6 : 3;

            foreach ($firstRoundMatches as $match) {
                $match->update([
                    'home_score' => $winningScore,
                    'away_score' => 0,
                ]);
            }

            $round = (new KnockoutBracketBuilder($knockout))->randomizeNextRound();

            $this->assertCount(1, $round->matches()->get());
            $this->assertCount(2, $round->matches()->first()->previousMatches()->get());
        }
    }

    private function createKnockout(KnockoutType $type): Knockout
    {
        return Knockout::create([
            'season_id' => Season::factory()->create()->id,
            'name' => "{$type->value} Cup",
            'type' => $type,
            'best_of' => 5,
        ]);
    }
}
