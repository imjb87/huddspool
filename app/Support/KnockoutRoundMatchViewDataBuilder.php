<?php

namespace App\Support;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use Illuminate\Support\Collection;

class KnockoutRoundMatchViewDataBuilder
{
    /**
     * @param  Collection<int, KnockoutMatch>  $matches
     * @param  array<int, int>  $matchNumbers
     * @return Collection<int, object>
     */
    public function build(Collection $matches, Knockout $knockout, array $matchNumbers, callable $slotLabelResolver): Collection
    {
        return $matches->map(function (KnockoutMatch $match) use ($knockout, $matchNumbers, $slotLabelResolver): object {
            $homeLabel = $slotLabelResolver($match, 'home');
            $awayLabel = $slotLabelResolver($match, 'away');
            $hasBye = ($match->home_participant_id && ! $match->away_participant_id)
                || ($match->away_participant_id && ! $match->home_participant_id);

            return (object) [
                'match' => $match,
                'match_label' => isset($matchNumbers[$match->id]) ? 'Match '.$matchNumbers[$match->id] : null,
                'home_label' => $homeLabel,
                'away_label' => $awayLabel,
                'has_bye' => $hasBye,
                'home_parts' => $this->participantParts($match->homeParticipant, $homeLabel, $knockout->type),
                'away_parts' => $this->participantParts($match->awayParticipant, $awayLabel, $knockout->type),
            ];
        });
    }

    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    private function participantParts(?KnockoutParticipant $participant, string $fallbackLabel, KnockoutType $type): array
    {
        if (! $participant) {
            return [
                ['label' => $fallbackLabel, 'url' => null],
            ];
        }

        if ($type === KnockoutType::Doubles) {
            return [
                [
                    'label' => $participant->playerOne?->name ?? $fallbackLabel,
                    'url' => $participant->playerOne ? route('player.show', $participant->playerOne) : null,
                ],
                [
                    'label' => $participant->playerTwo?->name ?? 'TBC',
                    'url' => $participant->playerTwo ? route('player.show', $participant->playerTwo) : null,
                ],
            ];
        }

        if ($participant->playerOne) {
            return [
                ['label' => $fallbackLabel, 'url' => route('player.show', $participant->playerOne)],
            ];
        }

        if ($participant->team) {
            return [
                ['label' => $fallbackLabel, 'url' => route('team.show', $participant->team)],
            ];
        }

        return [
            ['label' => $fallbackLabel, 'url' => null],
        ];
    }
}
