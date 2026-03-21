<?php

namespace App\Policies;

use App\Models\KnockoutMatch;
use App\Models\User;

class KnockoutMatchPolicy
{
    public function openSubmission(User $user, KnockoutMatch $match): bool
    {
        if ($match->winner_participant_id) {
            return false;
        }

        return $match->userCanSubmit($user);
    }

    public function submitResult(User $user, KnockoutMatch $match): bool
    {
        if ($match->winner_participant_id) {
            return false;
        }

        return $match->userCanSubmit($user);
    }
}
