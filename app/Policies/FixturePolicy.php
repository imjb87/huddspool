<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Fixture;
use App\Models\User;

class FixturePolicy
{
    public function createResult(User $user, Fixture $fixture): bool
    {
        if (! $user->can(PermissionName::SubmitLeagueResults->value)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $this->isOnFixtureTeam($user, $fixture);
    }

    public function submitResult(User $user, Fixture $fixture): bool
    {
        if ($user->isAdmin()) {
            return $user->can(PermissionName::SubmitLeagueResults->value);
        }

        if (! $this->isOnFixtureTeam($user, $fixture)) {
            return false;
        }

        return $user->can(PermissionName::SubmitLeagueResults->value);
    }

    private function isOnFixtureTeam(User $user, Fixture $fixture): bool
    {
        $teamId = $user->team?->id;

        if ($teamId === null) {
            return false;
        }

        return $teamId === $fixture->homeTeam?->id || $teamId === $fixture->awayTeam?->id;
    }
}
