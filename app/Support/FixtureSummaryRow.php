<?php

namespace App\Support;

use App\Data\TeamFixtureData;
use App\Models\Fixture;

class FixtureSummaryRow
{
    public static function fromFixture(Fixture $fixture, int $teamId, ?string $actionUrl = null, ?string $actionLabel = null): object
    {
        return self::build(
            fixtureId: $fixture->id,
            isBye: $fixture->isBye(),
            rowUrl: $fixture->isBye()
                ? null
                : ($actionUrl ?: ($fixture->result ? route('result.show', $fixture->result) : route('fixture.show', $fixture))),
            homeTeamId: $fixture->home_team_id,
            awayTeamId: $fixture->away_team_id,
            homeTeamName: $fixture->homeTeam?->name,
            awayTeamName: $fixture->awayTeam?->name,
            homeTeamShortname: $fixture->homeTeam?->shortname,
            awayTeamShortname: $fixture->awayTeam?->shortname,
            resultId: $fixture->result?->id,
            homeScore: $fixture->result?->home_score,
            awayScore: $fixture->result?->away_score,
            fixtureDateLabel: optional($fixture->fixture_date)->format('j M Y') ?? 'Date TBC',
            compactDateLabel: optional($fixture->fixture_date)->format('j M') ?? 'TBC',
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            teamId: $teamId,
        );
    }

    public static function fromTeamFixtureData(TeamFixtureData $fixture, int $teamId): object
    {
        return self::build(
            fixtureId: $fixture->id,
            isBye: $fixture->isBye(),
            rowUrl: $fixture->isBye()
                ? null
                : ($fixture->result_id ? route('result.show', $fixture->result_id) : route('fixture.show', $fixture->id)),
            homeTeamId: $fixture->home_team_id,
            awayTeamId: $fixture->away_team_id,
            homeTeamName: $fixture->home_team_name,
            awayTeamName: $fixture->away_team_name,
            homeTeamShortname: $fixture->home_team_shortname,
            awayTeamShortname: $fixture->away_team_shortname,
            resultId: $fixture->result_id,
            homeScore: $fixture->home_score,
            awayScore: $fixture->away_score,
            fixtureDateLabel: optional($fixture->fixture_date)->format('j M Y') ?? 'Date TBC',
            compactDateLabel: optional($fixture->fixture_date)->format('j M') ?? 'TBC',
            actionUrl: null,
            actionLabel: null,
            teamId: $teamId,
        );
    }

    private static function build(
        int $fixtureId,
        bool $isBye,
        ?string $rowUrl,
        int $homeTeamId,
        int $awayTeamId,
        ?string $homeTeamName,
        ?string $awayTeamName,
        ?string $homeTeamShortname,
        ?string $awayTeamShortname,
        ?int $resultId,
        ?int $homeScore,
        ?int $awayScore,
        string $fixtureDateLabel,
        string $compactDateLabel,
        ?string $actionUrl,
        ?string $actionLabel,
        int $teamId,
    ): object {
        $isDraw = $resultId !== null && (int) $homeScore === (int) $awayScore;
        $isActionable = $actionUrl !== null;
        $teamWon = $resultId !== null
            && (($homeTeamId === $teamId && (int) $homeScore > (int) $awayScore)
            || ($awayTeamId === $teamId && (int) $awayScore > (int) $homeScore));

        $resultPillClasses = $isDraw
            ? 'ui-score-pill-draw'
            : ($teamWon
                ? 'ui-score-pill-success'
                : 'ui-score-pill-danger');

        return (object) [
            'fixture_id' => $fixtureId,
            'row_url' => $rowUrl,
            'home_team_name' => $homeTeamName,
            'away_team_name' => $awayTeamName,
            'home_team_shortname' => $homeTeamShortname,
            'away_team_shortname' => $awayTeamShortname,
            'result_id' => $resultId,
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'result_pill_classes' => $resultPillClasses,
            'fixture_date_label' => $fixtureDateLabel,
            'compact_date_label' => $compactDateLabel,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
            'shows_inline_action' => $isActionable && $rowUrl !== $actionUrl,
            'is_actionable' => $isActionable,
            'is_bye' => $isBye,
        ];
    }
}
