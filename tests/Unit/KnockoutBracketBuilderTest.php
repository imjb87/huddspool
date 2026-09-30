<?php

namespace Tests\Unit;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
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

    public function test_a_populated_round_is_randomised_from_its_current_participants_without_changing_previous_results(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        collect(range(1, 64))->each(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        $builder = new KnockoutBracketBuilder($knockout);
        $builder->generate();

        $rounds = $knockout->rounds()->orderBy('position')->get();
        $previousRound = $rounds[1];
        $selectedRound = $rounds[2];

        $this->completeRound($rounds[0]);
        $this->completeRound($previousRound);

        $participantsBefore = $this->roundParticipantIds($selectedRound);
        $previousResultsBefore = $this->roundMatchSnapshot($previousRound);

        $this->assertCount(16, $participantsBefore);
        $this->assertCount(16, array_unique($participantsBefore));
        $this->assertCount(8, $selectedRound->matches()->get());

        $redrawnRound = $builder->randomizeRound($selectedRound);
        $participantsAfter = $this->roundParticipantIds($redrawnRound);

        $this->assertCount(16, $participantsAfter);
        $this->assertCount(16, array_unique($participantsAfter));
        $this->assertEqualsCanonicalizing($participantsBefore, $participantsAfter);
        $this->assertTrue($redrawnRound->matches->every(fn (KnockoutMatch $match): bool => $match->home_participant_id !== null
            && $match->away_participant_id !== null));
        $this->assertSame($previousResultsBefore, $this->roundMatchSnapshot($previousRound));
        $this->assertSame(64, $knockout->participants()->count());
    }

    public function test_a_fully_populated_round_does_not_depend_on_previous_round_slot_capacity(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        $participants = collect(range(1, 34))->map(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));
        $previousRound = $knockout->rounds()->create(['name' => 'Round 2', 'position' => 2]);
        $selectedRound = $knockout->rounds()->create(['name' => 'Round 3', 'position' => 3]);
        $previousMatches = collect(range(0, 16))->map(function (int $index) use ($knockout, $participants, $previousRound): KnockoutMatch {
            $match = KnockoutMatch::create([
                'knockout_id' => $knockout->id,
                'knockout_round_id' => $previousRound->id,
                'position' => $index + 1,
                'home_participant_id' => $participants[$index * 2]->id,
                'away_participant_id' => $participants[($index * 2) + 1]->id,
            ]);
            $match->update(['home_score' => 3, 'away_score' => 0]);

            return $match->fresh();
        });
        $currentParticipants = $previousMatches->take(16)->pluck('winner_participant_id')->values();

        foreach (range(0, 7) as $matchIndex) {
            KnockoutMatch::create([
                'knockout_id' => $knockout->id,
                'knockout_round_id' => $selectedRound->id,
                'position' => $matchIndex + 1,
                'home_participant_id' => $currentParticipants[$matchIndex * 2],
                'away_participant_id' => $currentParticipants[($matchIndex * 2) + 1],
            ]);
        }

        $participantsBefore = $this->roundParticipantIds($selectedRound);
        $previousResultsBefore = $this->roundMatchSnapshot($previousRound);

        $redrawnRound = (new KnockoutBracketBuilder($knockout))->randomizeRound($selectedRound);
        $participantsAfter = $this->roundParticipantIds($redrawnRound);

        $this->assertCount(17, $previousRound->matches()->get());
        $this->assertCount(16, $participantsAfter);
        $this->assertCount(16, array_unique($participantsAfter));
        $this->assertEqualsCanonicalizing($participantsBefore, $participantsAfter);
        $this->assertSame($previousResultsBefore, $this->roundMatchSnapshot($previousRound));
    }

    public function test_randomising_an_empty_selected_round_does_not_import_previous_round_participants(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        collect(range(1, 4))->each(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        $builder = new KnockoutBracketBuilder($knockout);
        $builder->generate();
        $rounds = $knockout->rounds()->orderBy('position')->get();
        $previousRound = $rounds[0];
        $selectedRound = $rounds[1];
        $this->completeRound($previousRound);
        $previousResultsBefore = $this->roundMatchSnapshot($previousRound);

        KnockoutMatch::query()
            ->whereKey($selectedRound->matches()->pluck('id'))
            ->update([
                'home_participant_id' => null,
                'away_participant_id' => null,
                'winner_participant_id' => null,
                'completed_at' => null,
            ]);

        $redrawnRound = $builder->randomizeRound($selectedRound);

        $this->assertSame([], $this->roundParticipantIds($redrawnRound));
        $this->assertSame($previousResultsBefore, $this->roundMatchSnapshot($previousRound));
    }

    public function test_randomize_next_round_still_advances_winners_into_an_empty_round(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        collect(range(1, 4))->each(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        $builder = new KnockoutBracketBuilder($knockout);
        $builder->generate();
        $rounds = $knockout->rounds()->orderBy('position')->get();
        $previousRound = $rounds[0];
        $selectedRound = $rounds[1];
        $this->completeRound($previousRound);
        $winners = $previousRound->matches()->pluck('winner_participant_id')->all();

        KnockoutMatch::query()
            ->whereKey($selectedRound->matches()->pluck('id'))
            ->update([
                'home_participant_id' => null,
                'away_participant_id' => null,
                'winner_participant_id' => null,
                'completed_at' => null,
            ]);

        $redrawnRound = $builder->randomizeNextRound();

        $this->assertSame($selectedRound->id, $redrawnRound->id);
        $this->assertEqualsCanonicalizing($winners, $this->roundParticipantIds($redrawnRound));
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

    public function test_a_round_cannot_be_redrawn_after_participants_have_reached_a_later_round(): void
    {
        $knockout = $this->createKnockout(KnockoutType::Singles);
        $participants = collect(range(1, 8))->map(fn (int $number) => KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => "Player {$number}",
        ]));

        (new KnockoutBracketBuilder($knockout))->generate();
        $rounds = $knockout->rounds()->orderBy('position')->get();
        $firstRound = $rounds[0];
        $secondRound = $rounds[1];
        $laterMatch = $secondRound->matches()->first();

        KnockoutMatch::query()
            ->whereKey($laterMatch->id)
            ->update(['home_participant_id' => $participants->first()->id]);

        $builder = new KnockoutBracketBuilder($knockout);

        $this->assertFalse($builder->canRandomizeRound($firstRound));

        $this->expectException(ValidationException::class);

        $builder->randomizeRound($firstRound);
    }

    public function test_an_incomplete_round_cannot_be_redrawn_after_a_bye_has_fed_the_later_round(): void
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

        $builder = new KnockoutBracketBuilder($knockout);

        $this->assertNotNull($firstMatch->fresh()->winner_participant_id);
        $this->assertFalse($builder->canRandomizeRound($secondRound));

        $this->expectException(ValidationException::class);

        $builder->randomizeRound($secondRound);
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

    private function completeRound(KnockoutRound $round): void
    {
        foreach ($round->matches()->get() as $match) {
            $match->update([
                'home_score' => 3,
                'away_score' => 0,
            ]);
        }
    }

    /**
     * @return array<int, int>
     */
    private function roundParticipantIds(KnockoutRound $round): array
    {
        return $round->matches()
            ->get()
            ->flatMap(fn (KnockoutMatch $match): array => [$match->home_participant_id, $match->away_participant_id])
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function roundMatchSnapshot(KnockoutRound $round): array
    {
        return $round->matches()->get()
            ->map(fn (KnockoutMatch $match): array => [
                'id' => $match->id,
                'home_participant_id' => $match->home_participant_id,
                'away_participant_id' => $match->away_participant_id,
                'winner_participant_id' => $match->winner_participant_id,
                'home_score' => $match->home_score,
                'away_score' => $match->away_score,
                'forfeit_participant_id' => $match->forfeit_participant_id,
                'forfeit_reason' => $match->forfeit_reason,
                'completed_at' => $match->completed_at?->toAtomString(),
                'reported_by_id' => $match->reported_by_id,
                'reported_at' => $match->reported_at?->toAtomString(),
                'report_reason' => $match->report_reason,
                'next_match_id' => $match->next_match_id,
                'next_slot' => $match->next_slot,
                'updated_at' => $match->updated_at?->toAtomString(),
            ])
            ->all();
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
