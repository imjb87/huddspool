<?php

namespace App\Services;

use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use Illuminate\Database\Eloquent\Collection;
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

        $bracketSize = $this->calculateBracketSize($participants->count());
        $slots = $this->padParticipants($participants, $bracketSize);
        $roundCount = (int) log($bracketSize, 2);

        DB::transaction(function () use ($roundCount, $bracketSize, $slots) {
            $this->knockout->matches()->delete();
            $rounds = $this->ensureRounds($roundCount, $bracketSize);
            $matchesByRound = [];

            // First round
            $firstRound = $rounds[0];
            $matchesByRound[0] = collect();

            for ($i = 0; $i < $bracketSize; $i += 2) {
                $match = KnockoutMatch::create([
                    'knockout_id' => $this->knockout->id,
                    'knockout_round_id' => $firstRound->id,
                    'position' => ($i / 2) + 1,
                    'home_participant_id' => $slots[$i]?->id,
                    'away_participant_id' => $slots[$i + 1]?->id,
                ]);

                $matchesByRound[0]->push($match);
            }

            // Subsequent rounds
            for ($roundIndex = 1; $roundIndex < $roundCount; $roundIndex++) {
                $round = $rounds[$roundIndex];
                $matchesByRound[$roundIndex] = collect();
                $previousMatches = $matchesByRound[$roundIndex - 1];

                for ($i = 0; $i < $previousMatches->count(); $i += 2) {
                    $match = KnockoutMatch::create([
                        'knockout_id' => $this->knockout->id,
                        'knockout_round_id' => $round->id,
                        'position' => ($i / 2) + 1,
                    ]);

                    $matchesByRound[$roundIndex]->push($match);

                    $homeSource = $previousMatches[$i];
                    $homeSource->update([
                        'next_match_id' => $match->id,
                        'next_slot' => 'home',
                    ]);

                    if (isset($previousMatches[$i + 1])) {
                        $awaySource = $previousMatches[$i + 1];
                        $awaySource->update([
                            'next_match_id' => $match->id,
                            'next_slot' => 'away',
                        ]);
                    }
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

    /**
     * @return KnockoutParticipant[]
     */
    private function padParticipants(Collection $participants, int $size): array
    {
        $slots = $participants->all();

        while (count($slots) < $size) {
            $slots[] = null;
        }

        return $slots;
    }

    private function ensureRounds(int $roundCount, int $bracketSize): array
    {
        $rounds = $this->knockout->rounds()->orderBy('position')->take($roundCount)->get();

        $position = $rounds->count() ? $rounds->last()->position + 1 : 1;

        while ($rounds->count() < $roundCount) {
            $remainingPlayers = $bracketSize / (2 ** $rounds->count());

            $rounds->push($this->knockout->rounds()->create([
                'name' => $this->defaultRoundName($remainingPlayers),
                'position' => $position,
            ]));

            $position++;
        }

        return $rounds->sortBy('position')->values()->all();
    }

    private function defaultRoundName(int $remainingPlayers): string
    {
        return match ($remainingPlayers) {
            2 => 'Final',
            4 => 'Semi Finals',
            8 => 'Quarter Finals',
            default => "Round of {$remainingPlayers}",
        };
    }
}
