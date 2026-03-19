<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function updateProfile(User $user, User $player): bool
    {
        return $user->is($player) || $user->isAdmin();
    }

    public function updateAvatar(User $user, User $player): bool
    {
        return $user->is($player) || $user->isAdmin();
    }
}
