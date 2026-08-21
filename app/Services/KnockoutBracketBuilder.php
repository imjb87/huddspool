<?php

namespace App\Services;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KnockoutBracketBuilder
{
    public function __construct(protected Knockout $knockout) {}

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
                if ($this->knockout->type === KnockoutType::Team && ! str_contains(strtolower($firstRound->name), 'semi') && ! str_contains(strtolower($firstRound->name), 'final')) {
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
                    'starts_at' => ($firstRound->scheduled_for ?? now())->copy()->setTime(20, 15),
                ]);

                $matchesByRound[0]->push($match);
            }

            foreach ($byeParticipants as $participant) {
                $venueId = null;
                if ($this->knockout->type === KnockoutType::Team && ! str_contains(strtolower($firstRound->name), 'semi') && ! str_contains(strtolower($firstRound->name), 'final')) {
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
                    'starts_at' => ($firstRound->scheduled_for ?? now())->copy()->setTime(20, 15),
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
                        'starts_at' => ($round->scheduled_for ?? now())->copy()->setTime(20, 15),
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

    public function randomizeNextRound(): KnockoutRound
    {
        $rounds = $this->knockout->rounds()
            ->with('matches')
            ->orderBy('position')
            ->get()
            ->values();

        foreach ($rounds as $roundIndex => $round) {
            if ($roundIndex === 0) {
                continue;
            }

            $previousRound = $rounds->get($roundIndex - 1);

            if (! $previousRound || ! $this->roundIsComplete($previousRound) || ! $this->canRandomizeRound($round)) {
                continue;
            }

            return $this->randomizeRound($round);
        }

        throw ValidationException::withMessages([
            'draw' => 'Complete the current knockout round before randomising the next draw.',
        ]);
    }

    public function randomizeRound(KnockoutRound $round): KnockoutRound
    {
        $round->loadMissing('matches');

        if (! $this->canRandomizeRound($round)) {
            throw ValidationException::withMessages([
                'draw' => 'Only incomplete rounds without recorded results can be randomised.',
            ]);
        }

        $previousRound = $this->previousRound($round);

        if ($previousRound) {
            $this->redrawRound($previousRound->matches, $round->matches);
        } else {
            $this->redrawFirstRound($round);
        }

        $round->refresh();
        $round->load('matches');

        return $round;
    }

    public function canRandomizeRound(KnockoutRound $round): bool
    {
        $round->loadMissing('matches');

        if ((int) $round->knockout_id !== (int) $this->knockout->id || $round->matches->isEmpty()) {
            return false;
        }

        if ($round->matches->contains(fn (KnockoutMatch $match): bool => $this->matchHasRecordedResult($match))) {
            return false;
        }

        if (! $round->matches->contains(fn (KnockoutMatch $match): bool => $this->matchIsIncomplete($match))) {
            return false;
        }

        return ! $this->laterRoundsHaveResults($round);
    }

    private function previousRound(KnockoutRound $round): ?KnockoutRound
    {
        return $this->knockout->rounds()
            ->where('position', '<', $round->position)
            ->orderByDesc('position')
            ->with('matches.winner.team')
            ->first();
    }

    private function roundIsComplete(KnockoutRound $round): bool
    {
        return $round->matches->isNotEmpty()
            && $round->matches->every(fn (KnockoutMatch $match): bool => $match->completed_at !== null && $match->winner_participant_id !== null);
    }

    private function matchHasRecordedResult(KnockoutMatch $match): bool
    {
        return $match->home_score !== null
            || $match->away_score !== null
            || $match->forfeit_participant_id !== null;
    }

    private function matchIsIncomplete(KnockoutMatch $match): bool
    {
        return $match->completed_at === null || $match->winner_participant_id === null;
    }

    private function laterRoundsHaveResults(KnockoutRound $round): bool
    {
        return $this->knockout->rounds()
            ->where('position', '>', $round->position)
            ->with('matches')
            ->get()
            ->flatMap(fn (KnockoutRound $laterRound): Collection => $laterRound->matches)
            ->contains(fn (KnockoutMatch $match): bool => $this->matchHasRecordedResult($match));
    }

    private function redrawFirstRound(KnockoutRound $round): void
    {
        $participants = $this->knockout->participants()
            ->ordered()
            ->with('team')
            ->get()
            ->shuffle()
            ->values();

        if ($participants->count() > $round->matches->count() * 2) {
            throw ValidationException::withMessages([
                'draw' => 'There are not enough match slots for all knockout participants.',
            ]);
        }

        DB::transaction(function () use ($participants, $round): void {
            foreach ($round->matches as $matchIndex => $match) {
                $homeParticipant = $participants->get($matchIndex * 2);
                $awayParticipant = $participants->get(($matchIndex * 2) + 1);

                $match->fill([
                    'home_participant_id' => $homeParticipant?->id,
                    'away_participant_id' => $awayParticipant?->id,
                    'winner_participant_id' => null,
                    'home_score' => null,
                    'away_score' => null,
                    'forfeit_participant_id' => null,
                    'forfeit_reason' => null,
                    'reported_by_id' => null,
                    'reported_at' => null,
                    'report_reason' => null,
                    'venue_id' => $this->venueIdForHomeParticipant($round, $homeParticipant?->id, $match->venue_id),
                ]);
                $match->save();
            }
        });
    }

    private function venueIdForHomeParticipant(KnockoutRound $round, ?int $participantId, ?int $fallback): ?int
    {
        if ($this->knockout->type !== KnockoutType::Team || $this->isNeutralRound($round)) {
            return $fallback;
        }

        if (! $participantId) {
            return null;
        }

        $participant = KnockoutParticipant::query()
            ->with('team')
            ->find($participantId);

        return $participant?->team?->venue_id;
    }

    private function isNeutralRound(KnockoutRound $round): bool
    {
        $roundName = strtolower($round->name);

        return str_contains($roundName, 'semi') || str_contains($roundName, 'final');
    }

    /**
     * @param  Collection<int, KnockoutMatch>  $previousMatches
     * @param  Collection<int, KnockoutMatch>  $roundMatches
     */
    private function redrawRound(Collection $previousMatches, Collection $roundMatches): void
    {
        $previousMatches = $previousMatches->values();

        if ($previousMatches->count() > ($roundMatches->count() * 2)) {
            throw ValidationException::withMessages([
                'draw' => 'There are not enough match slots for the previous round entries.',
            ]);
        }

        $shuffledPreviousMatches = $previousMatches->shuffle()->values();

        DB::transaction(function () use ($previousMatches, $roundMatches, $shuffledPreviousMatches): void {
            KnockoutMatch::query()
                ->whereKey($previousMatches->modelKeys())
                ->update([
                    'next_match_id' => null,
                    'next_slot' => null,
                ]);

            foreach ($roundMatches as $matchIndex => $match) {
                $homePreviousMatch = $shuffledPreviousMatches->get($matchIndex * 2);
                $awayPreviousMatch = $shuffledPreviousMatches->get(($matchIndex * 2) + 1);
                $homeParticipantId = $homePreviousMatch?->winner_participant_id;
                $awayParticipantId = $awayPreviousMatch?->winner_participant_id;

                $match->fill([
                    'home_participant_id' => $homeParticipantId,
                    'away_participant_id' => $awayParticipantId,
                    'winner_participant_id' => null,
                    'home_score' => null,
                    'away_score' => null,
                    'forfeit_participant_id' => null,
                    'forfeit_reason' => null,
                    'reported_by_id' => null,
                    'reported_at' => null,
                    'report_reason' => null,
                    'venue_id' => $this->venueIdForHomeParticipant($match->round, $homeParticipantId, $match->venue_id),
                ]);
                $match->suppressAutoBye();
                $match->save();

                foreach ([$homePreviousMatch, $awayPreviousMatch] as $slotIndex => $previousMatch) {
                    if (! $previousMatch) {
                        continue;
                    }

                    KnockoutMatch::query()
                        ->whereKey($previousMatch->id)
                        ->update([
                            'next_match_id' => $match->id,
                            'next_slot' => $slotIndex === 0 ? 'home' : 'away',
                        ]);
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
                'name' => 'Round '.($rounds->count() + 1),
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

        return 'Round '.($roundIndex + 1);
    }
}
