<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function updateAvatar(User $user, User $player): bool
    {
        return $user->is($player) || $user->isAdmin();
    }
}
