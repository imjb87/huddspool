<?php

namespace App\Services;

use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KnockoutBracketBuilder
{
    public function __construct(protected Knockout $knockout)
    {
    }

    public function generate(bool $shuffle = false): void
    {
        $participants = $this->knockout->participants()->ordered()->get();

        if ($shuffle) {
            $participants = $participants->shuffle()->values();
        }

        if ($participants->count() < 2) {
            throw ValidationException::withMessages([
                'participants' => 'Add at least two participants before generating matches.',
            ]);
        }

        $participantCount = $participants->count();
        $bracketSize = $this->calculateBracketSize($participantCount);
        $roundCount = (int) log($bracketSize, 2);
        $nextRoundSize = $bracketSize / 2;
        $firstRoundMatchCount = max($participantCount - $nextRoundSize, 0);
        $byeCount = max($participantCount - ($firstRoundMatchCount * 2), 0);

        $byeParticipants = $participants->take($byeCount);
        $firstRoundParticipants = $participants->slice($byeCount)->values();

        DB::transaction(function () use (
            $roundCount,
            $bracketSize,
            $firstRoundMatchCount,
            $firstRoundParticipants,
            $byeParticipants,
            $participants,
            $shuffle
        ) {
            if ($shuffle) {
                $this->persistRandomSeeds($participants);
            }

            $this->knockout->matches()->delete();
            $rounds = $this->ensureRounds($roundCount, $bracketSize);
            $matchesByRound = [];

            // First round
            $firstRound = $rounds[0];
            $matchesByRound[0] = collect();

            $position = 1;


            for ($i = 0; $i < $firstRoundMatchCount; $i++) {
                $home = $firstRoundParticipants[$i * 2] ?? null;
                $away = $firstRoundParticipants[$i * 2 + 1] ?? null;

                $venueId = null;
                // Set venue for team knockouts up to semi-finals
                if ($this->knockout->type === \App\KnockoutType::Team && !str_contains(strtolower($firstRound->name), 'semi') && !str_contains(strtolower($firstRound->name), 'final')) {
                    if ($home && $home->team) {
                        $venueId = $home->team->venue_id;
                    }
                }

                $match = KnockoutMatch::create([
                    'knockout_id' => $this->knockout->id,
                    'knockout_round_id' => $firstRound->id,
                    'position' => $position++,
                    'home_participant_id' => $home?->id,
                    'away_participant_id' => $away?->id,
                    'venue_id' => $venueId,
                ]);

                $matchesByRound[0]->push($match);
            }

            foreach ($byeParticipants as $participant) {
                $venueId = null;
                if ($this->knockout->type === \App\KnockoutType::Team && !str_contains(strtolower($firstRound->name), 'semi') && !str_contains(strtolower($firstRound->name), 'final')) {
                    if ($participant && $participant->team) {
                        $venueId = $participant->team->venue_id;
                    }
                }
                $match = KnockoutMatch::create([
                    'knockout_id' => $this->knockout->id,
                    'knockout_round_id' => $firstRound->id,
                    'position' => $position++,
                    'home_participant_id' => $participant?->id,
                    'away_participant_id' => null,
                    'venue_id' => $venueId,
                ]);

                $matchesByRound[0]->push($match);
            }

            $advancingEntries = $matchesByRound[0]->values();

            // Subsequent rounds
            for ($roundIndex = 1; $roundIndex < $roundCount; $roundIndex++) {
                $round = $rounds[$roundIndex];
                $matchesByRound[$roundIndex] = collect();
                $pairs = $advancingEntries->chunk(2);
                $advancingEntries = collect();

                foreach ($pairs as $pairIndex => $pair) {
                    $match = new KnockoutMatch([
                        'knockout_id' => $this->knockout->id,
                        'knockout_round_id' => $round->id,
                        'position' => $pairIndex + 1,
                    ]);

                    if ($pair->contains(fn ($entry) => $entry instanceof KnockoutMatch)) {
                        $match->suppressAutoBye();
                    }

                    $match->save();

                    $matchesByRound[$roundIndex]->push($match);

                    foreach ($pair->values() as $slotIndex => $entry) {
                        $slot = $slotIndex === 0 ? 'home' : 'away';

                        if ($entry instanceof KnockoutParticipant) {
                            $match->update([
                                "{$slot}_participant_id" => $entry->id,
                            ]);
                        } elseif ($entry instanceof KnockoutMatch) {
                            $entry->update([
                                'next_match_id' => $match->id,
                                'next_slot' => $slot,
                            ]);
                        }
                    }

                    $advancingEntries->push($match);
                }
            }
        });
    }

    private function calculateBracketSize(int $participants): int
    {
        $size = 1;

        while ($size < $participants) {
            $size *= 2;
        }

        return $size;
    }

    private function ensureRounds(int $roundCount, int $bracketSize): array
    {
        $rounds = $this->knockout->rounds()->orderBy('position')->take($roundCount)->get();

        $position = $rounds->count() ? $rounds->last()->position + 1 : 1;

        while ($rounds->count() < $roundCount) {
            $rounds->push($this->knockout->rounds()->create([
                'name' => "Round " . ($rounds->count() + 1),
                'position' => $position,
            ]));

            $position++;
        }

        $rounds = $this->knockout->rounds()->orderBy('position')->take($roundCount)->get()->values();

        foreach ($rounds as $index => $round) {
            $remainingPlayers = $bracketSize / (2 ** $index);
            $name = $this->defaultRoundName($index, $remainingPlayers);

            if ($round->name !== $name) {
                $round->update(['name' => $name]);
            }
        }

        return $rounds->all();
    }

    private function persistRandomSeeds(Collection $participants): void
    {
        foreach ($participants->values() as $index => $participant) {
            $participant->forceFill([
                'seed' => $index + 1,
            ])->save();
        }
    }

    private function defaultRoundName(int $roundIndex, int $remainingPlayers): string
    {
        if ($remainingPlayers <= 2) {
            return 'Final';
        }

        if ($remainingPlayers <= 4) {
            return 'Semi Finals';
        }

        if ($remainingPlayers <= 8) {
            return 'Quarter Finals';
        }

        return 'Round ' . ($roundIndex + 1);
    }
}
