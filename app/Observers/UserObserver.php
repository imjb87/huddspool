<?php

namespace App\Observers;

use App\Models\User;
use App\Support\CompetitionCacheInvalidator;

class UserObserver
{
    public function saved(User $user): void
    {
        $this->flushCaches($user);
    }

    public function deleted(User $user): void
    {
        $this->flushCaches($user);
    }

    public function restored(User $user): void
    {
        $this->flushCaches($user);
    }

    protected function flushCaches(User $user): void
    {
        (new CompetitionCacheInvalidator)->forgetForUser($user);
    }
}
