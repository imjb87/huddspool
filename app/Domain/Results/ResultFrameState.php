<?php

namespace App\Domain\Results;

use Illuminate\Support\Collection;

final class ResultFrameState
{
    public const int FRAME_COUNT = 10;

    /**
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    public static function empty(): array
    {
        $frames = [];

        for ($i = 1; $i <= self::FRAME_COUNT; $i++) {
            $frames[$i] = self::emptyFrame();
        }

        return $frames;
    }

    /**
     * @param  array<int|string, array<string, mixed>>|null  $frames
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    public static function normalize(?array $frames): array
    {
        $frames ??= [];
        $normalized = [];

        for ($i = 1; $i <= self::FRAME_COUNT; $i++) {
            $frame = $frames[$i] ?? $frames[(string) $i] ?? [];

            $normalized[$i] = is_array($frame)
                ? self::normalizeFrame($frame)
                : self::emptyFrame();
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $frame
     * @return array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}
     */
    public static function normalizeFrame(array $frame): array
    {
        return [
            'home_player_id' => self::normalizePlayerId($frame['home_player_id'] ?? null),
            'away_player_id' => self::normalizePlayerId($frame['away_player_id'] ?? null),
            'home_score' => self::normalizeScore($frame['home_score'] ?? 0),
            'away_score' => self::normalizeScore($frame['away_score'] ?? 0),
        ];
    }

    /**
     * @param  Collection<int, object>  $frames
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    public static function fromPersistedFrames(Collection $frames): array
    {
        $state = self::empty();

        foreach ($frames->values() as $index => $frame) {
            $frameNumber = $index + 1;

            if ($frameNumber > self::FRAME_COUNT) {
                break;
            }

            $state[$frameNumber] = self::normalizeFrame([
                'home_player_id' => $frame->home_player_id,
                'away_player_id' => $frame->away_player_id,
                'home_score' => $frame->home_score,
                'away_score' => $frame->away_score,
            ]);
        }

        return $state;
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $draftFrames
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    public static function merge(array $state, array $draftFrames): array
    {
        $merged = self::normalize($state);

        for ($i = 1; $i <= self::FRAME_COUNT; $i++) {
            $draftFrame = $draftFrames[$i] ?? $draftFrames[(string) $i] ?? null;

            if (is_array($draftFrame)) {
                $merged[$i] = self::normalizeFrame($draftFrame);
            }
        }

        return $merged;
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $frames
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    public static function completed(array $frames): array
    {
        return collect(self::normalize($frames))
            ->filter(fn (array $frame): bool => self::isComplete($frame))
            ->all();
    }

    /**
     * @param  array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}  $frame
     */
    public static function isComplete(array $frame): bool
    {
        return $frame['home_player_id'] !== null
            && $frame['away_player_id'] !== null
            && in_array($frame['home_score'], [0, 1], true)
            && in_array($frame['away_score'], [0, 1], true)
            && ($frame['home_score'] + $frame['away_score']) === 1;
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $frames
     */
    public static function isEmpty(array $frames): bool
    {
        foreach (self::normalize($frames) as $frame) {
            if ($frame['home_player_id'] !== null || $frame['away_player_id'] !== null) {
                return false;
            }

            if ($frame['home_score'] > 0 || $frame['away_score'] > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $frames
     * @return array{home_score: int, away_score: int, total_score: int}
     */
    public static function scores(array $frames): array
    {
        $normalized = self::normalize($frames);
        $homeScore = array_sum(array_column($normalized, 'home_score'));
        $awayScore = array_sum(array_column($normalized, 'away_score'));

        return [
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'total_score' => $homeScore + $awayScore,
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $previousFrames
     * @param  array<int|string, array<string, mixed>>  $nextFrames
     * @return array<int, int>
     */
    public static function changedFrameNumbers(array $previousFrames, array $nextFrames): array
    {
        $previous = self::normalize($previousFrames);
        $next = self::normalize($nextFrames);
        $changed = [];

        for ($i = 1; $i <= self::FRAME_COUNT; $i++) {
            if ($previous[$i] !== $next[$i]) {
                $changed[] = $i;
            }
        }

        return $changed;
    }

    /**
     * @return array{home_player_id: null, away_player_id: null, home_score: int, away_score: int}
     */
    private static function emptyFrame(): array
    {
        return [
            'home_player_id' => null,
            'away_player_id' => null,
            'home_score' => 0,
            'away_score' => 0,
        ];
    }

    private static function normalizePlayerId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private static function normalizeScore(mixed $value): int
    {
        return (int) $value;
    }
}
