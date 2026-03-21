<?php

namespace App\Support;

use App\Models\KnockoutMatch;
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
        $canSubmit = ! $hasResult && Gate::allows('submitResult', $match);

        return self::build(
            match: $match,
            rowUrl: $canSubmit
                ? route('knockout.matches.submit', $match)
                : ($hasResult ? route('knockout.show', $match->round->knockout) : null),
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
            rowUrl: ! $hasResult && $allowSubmission && Gate::allows('submitResult', $match)
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
            ? 'bg-linear-to-br from-gray-700 via-gray-600 to-gray-500'
            : ($isDraw
                ? 'bg-linear-to-br from-gray-600 via-gray-500 to-gray-400'
                : ($wonMatch
                    ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                    : 'bg-linear-to-br from-red-800 via-red-700 to-red-600'));

        return (object) [
            'id' => $match->id,
            'row_url' => $rowUrl,
            'home_label' => $match->homeParticipant?->display_name ?? 'TBC',
            'away_label' => $match->awayParticipant?->display_name ?? 'TBC',
            'meta_label' => ($match->round?->knockout?->name ?? 'Knockout').' / '.($match->round?->name ?? 'Round TBC'),
            'has_result' => $hasResult,
            'home_score' => $match->home_score,
            'away_score' => $match->away_score,
            'result_pill_classes' => $resultPillClasses,
            'date_label' => $match->starts_at ? $match->starts_at->format('j M') : 'Date TBC',
        ];
    }

    private static function hasResult(KnockoutMatch $match): bool
    {
        return $match->home_score !== null && $match->away_score !== null;
    }
}
