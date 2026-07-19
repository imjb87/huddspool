<?php

namespace Tests\Unit;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\Season;
use App\Services\KnockoutBracketBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
