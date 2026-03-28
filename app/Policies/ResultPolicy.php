<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    public function resumeSubmission(User $user, Result $result): bool
    {
        $teamId = $user->team?->id;

        if ($teamId === null) {
            return false;
        }

        if ($teamId !== $result->home_team_id && $teamId !== $result->away_team_id) {
            return false;
        }

        return $user->can(PermissionName::SubmitLeagueResults->value);
    }
}
