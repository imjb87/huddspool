<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    public function resumeSubmission(User $user, Result $result): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isTeamAdmin()) {
            return false;
        }

        $teamId = $user->team?->id;

        if ($teamId === null) {
            return false;
        }

        return $teamId === $result->home_team_id || $teamId === $result->away_team_id;
    }
}
