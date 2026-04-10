<?php

namespace App\Support;

use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class KnockoutMatchSummaryRow
{
    public static function forUser(KnockoutMatch $match, User $user): object
    {
        $participantId = null;

        if ($match->homeParticipant?->includesPlayer($user) || $match->homeParticipant?->team_id === $user->team_id) {
            $participantId = $match->homeParticipant?->id;
        } elseif ($match->awayParticipant?->includesPlayer($user) || $match->awayParticipant?->team_id === $user->team_id) {
            $participantId = $match->awayParticipant?->id;
        }

        $hasResult = self::hasResult($match);
        $canSubmit = ! $hasResult && Gate::allows('openSubmission', $match);

        return self::build(
            match: $match,
            rowUrl: $canSubmit
                ? route('knockout.matches.submit', $match)
                : ($hasResult ? route('knockout.show', $match->round->knockout) : null),
            participantId: $participantId,
            neutralPill: false,
        );
    }

    public static function forPlayer(KnockoutMatch $match, User $player, bool $allowSubmission): object
    {
        $participantId = null;

        if ($match->homeParticipant?->includesPlayer($player)) {
            $participantId = $match->homeParticipant?->id;
        } elseif ($match->awayParticipant?->includesPlayer($player)) {
            $participantId = $match->awayParticipant?->id;
        }

        $hasResult = self::hasResult($match);

        return self::build(
            match: $match,
            rowUrl: ! $hasResult && $allowSubmission && Gate::allows('openSubmission', $match)
                ? route('knockout.matches.submit', $match)
                : route('knockout.show', $match->round->knockout),
            participantId: $participantId,
            neutralPill: false,
        );
    }

    public static function forTeam(KnockoutMatch $match, Team $team, bool $allowSubmission): object
    {
        $participantId = null;

        if ($match->homeParticipant?->team_id === $team->id) {
            $participantId = $match->homeParticipant?->id;
        } elseif ($match->awayParticipant?->team_id === $team->id) {
            $participantId = $match->awayParticipant?->id;
        }

        $hasResult = self::hasResult($match);

        return self::build(
            match: $match,
            rowUrl: ! $hasResult && $allowSubmission && Gate::allows('openSubmission', $match)
                ? route('knockout.matches.submit', $match)
                : ($hasResult ? route('knockout.show', $match->round->knockout) : null),
            participantId: $participantId,
            neutralPill: false,
        );
    }

    public static function neutral(KnockoutMatch $match): object
    {
        return self::build(
            match: $match,
            rowUrl: route('knockout.show', $match->round->knockout),
            participantId: null,
            neutralPill: true,
        );
    }

    private static function build(KnockoutMatch $match, ?string $rowUrl, ?int $participantId, bool $neutralPill): object
    {
        $hasResult = self::hasResult($match);
        $isDraw = $hasResult && (int) $match->home_score === (int) $match->away_score;
        $wonMatch = $hasResult && $match->winner_participant_id && $participantId === $match->winner_participant_id;

        $resultPillClasses = $neutralPill
            ? 'ui-score-pill-neutral'
            : ($isDraw
                ? 'ui-score-pill-draw'
                : ($wonMatch
                    ? 'ui-score-pill-success'
                    : 'ui-score-pill-danger'));

        $homeIsWinner = $match->winner_participant_id !== null
            && $match->home_participant_id !== null
            && (int) $match->winner_participant_id === (int) $match->home_participant_id;
        $awayIsWinner = $match->winner_participant_id !== null
            && $match->away_participant_id !== null
            && (int) $match->winner_participant_id === (int) $match->away_participant_id;
        $homeIsLoser = $match->winner_participant_id !== null
            && $match->home_participant_id !== null
            && $match->away_participant_id !== null
            && ! $homeIsWinner;
        $awayIsLoser = $match->winner_participant_id !== null
            && $match->home_participant_id !== null
            && $match->away_participant_id !== null
            && ! $awayIsWinner;

        return (object) [
            'id' => $match->id,
            'row_url' => $rowUrl,
            'home_label' => $match->homeParticipant?->display_name ?? 'TBC',
            'away_label' => $match->awayParticipant?->display_name ?? 'TBC',
            'home_label_classes' => $homeIsLoser
                ? 'text-gray-400 dark:text-gray-500'
                : 'text-gray-900 dark:text-gray-100',
            'away_label_classes' => $awayIsLoser
                ? 'text-gray-400 dark:text-gray-500'
                : 'text-gray-900 dark:text-gray-100',
            'is_doubles' => $match->type() === KnockoutType::Doubles,
            'home_parts' => self::participantParts($match->homeParticipant, $match->homeParticipant?->display_name ?? 'TBC', $match->type()),
            'away_parts' => self::participantParts($match->awayParticipant, $match->awayParticipant?->display_name ?? 'TBC', $match->type()),
            'meta_label' => ($match->round?->knockout?->name ?? 'Knockout').' / '.($match->round?->name ?? 'Round TBC'),
            'venue_label' => $match->venue?->name ?? 'Venue TBC',
            'referee_label' => $match->referee,
            'has_result' => $hasResult,
            'home_score' => $match->home_score,
            'away_score' => $match->away_score,
            'result_pill_classes' => $resultPillClasses,
            'date_label' => $match->starts_at ? $match->starts_at->format('j M \a\t H:i') : 'Date TBC',
        ];
    }

    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    private static function participantParts(?KnockoutParticipant $participant, string $fallbackLabel, ?KnockoutType $type): array
    {
        if (! $participant) {
            return [
                ['label' => $fallbackLabel, 'url' => null],
            ];
        }

        if ($type === KnockoutType::Doubles) {
            return [
                [
                    'label' => $participant->playerOne?->name ?? 'TBC',
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

    private static function hasResult(KnockoutMatch $match): bool
    {
        return $match->home_score !== null && $match->away_score !== null;
    }
}
