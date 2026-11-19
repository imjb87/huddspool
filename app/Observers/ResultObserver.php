<?php

namespace App\Observers;

use App\Domain\Results\Notifications\LeagueResultSubmissionMailer;
use App\Models\Result;

class ResultObserver
{
    public function saved(Result $result): void
    {
        (new LeagueResultSubmissionMailer)->sendIfNeeded($result);
    }
}
