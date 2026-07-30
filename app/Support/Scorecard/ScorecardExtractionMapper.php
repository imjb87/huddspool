<?php

namespace App\Support\Scorecard;

use App\Models\Fixture;

class ScorecardExtractionMapper
{
    public function __construct(private readonly ScorecardPlayerResolver $resolver) {}

    /**
     * Map an extraction result onto the FixtureResultForm frame structure by
     * resolving extracted player names against the fixture's eligible players.
     *
     * @return array{
     *   frames: array<int, array{home_player_id: int|null, away_player_id: int|null, home_score: int, away_score: int}>,
     *   warnings: list<string>
     * }
     */
    public function map(ScorecardExtractionResult $extraction, Fixture $fixture): array
    {
        $warnings = $extraction->warnings;
        $frames = [];

        $homePlayers = $fixture->homeTeam->players;
        $awayPlayers = $fixture->awayTeam->players;

        foreach ($extraction->frames as $frameNumber => $extractedFrame) {
            $frameNumber = (int) $frameNumber;

            if ($frameNumber < 1 || $frameNumber > 10) {
                continue;
            }

            $homePlayerName = trim((string) ($extractedFrame['home_player_name'] ?? ''));
            $awayPlayerName = trim((string) ($extractedFrame['away_player_name'] ?? ''));

            $homeResolution = $homePlayerName !== ''
                ? $this->resolver->resolve($homePlayerName, $homePlayers, $frameNumber, 'home')
                : ['id' => null, 'warning' => null];

            $awayResolution = $awayPlayerName !== ''
                ? $this->resolver->resolve($awayPlayerName, $awayPlayers, $frameNumber, 'away')
                : ['id' => null, 'warning' => null];

            if ($homeResolution['warning'] !== null) {
                $warnings[] = $homeResolution['warning'];
            }

            if ($awayResolution['warning'] !== null) {
                $warnings[] = $awayResolution['warning'];
            }

            $homeScore = (int) ($extractedFrame['home_score'] ?? 0);
            $awayScore = (int) ($extractedFrame['away_score'] ?? 0);

            // Clamp to valid 0/1 per-frame score values.
            $homeScore = in_array($homeScore, [0, 1], true) ? $homeScore : 0;
            $awayScore = in_array($awayScore, [0, 1], true) ? $awayScore : 0;

            $frames[$frameNumber] = [
                'home_player_id' => $homeResolution['id'],
                'away_player_id' => $awayResolution['id'],
                'home_score' => $homeScore,
                'away_score' => $awayScore,
            ];
        }

        return [
            'frames' => $frames,
            'warnings' => array_values($warnings),
        ];
    }
}
