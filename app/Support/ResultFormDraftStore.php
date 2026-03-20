<?php

namespace App\Support;

class ResultFormDraftStore
{
    /**
     * @param  array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null  $frames
     * @return array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null
     */
    public function get(int $userId, int $fixtureId): ?array
    {
        $draftFrames = session($this->sessionKey($userId, $fixtureId));

        return is_array($draftFrames) ? $draftFrames : null;
    }

    /**
     * @param  array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>  $frames
     */
    public function put(int $userId, int $fixtureId, array $frames): void
    {
        if ($this->isEmpty($frames)) {
            $this->forget($userId, $fixtureId);

            return;
        }

        session()->put($this->sessionKey($userId, $fixtureId), $frames);
    }

    public function forget(int $userId, int $fixtureId): void
    {
        session()->forget($this->sessionKey($userId, $fixtureId));
    }

    /**
     * @param  array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>  $frames
     */
    public function isEmpty(array $frames): bool
    {
        foreach ($frames as $frame) {
            $homePlayerId = $frame['home_player_id'] ?? null;
            $awayPlayerId = $frame['away_player_id'] ?? null;
            $homeScore = (int) ($frame['home_score'] ?? 0);
            $awayScore = (int) ($frame['away_score'] ?? 0);

            if ($homePlayerId !== null && $homePlayerId !== '') {
                return false;
            }

            if ($awayPlayerId !== null && $awayPlayerId !== '') {
                return false;
            }

            if ($homeScore > 0 || $awayScore > 0) {
                return false;
            }
        }

        return true;
    }

    private function sessionKey(int $userId, int $fixtureId): string
    {
        return 'result-form-draft:'.$userId.':'.$fixtureId;
    }
}
