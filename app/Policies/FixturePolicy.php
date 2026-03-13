<?php

namespace App\Policies;

use App\Models\Fixture;
use App\Models\User;

class FixturePolicy
{
    public function createResult(User $user, Fixture $fixture): bool
    {
        if (! $user->isTeamAdmin()) {
            return false;
        }

        return $this->isOnFixtureTeam($user, $fixture);
    }

    public function submitResult(User $user, Fixture $fixture): bool
    {
        if (! $this->isOnFixtureTeam($user, $fixture)) {
            return false;
        }

        return $user->isTeamAdmin() || $user->isAdmin();
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
