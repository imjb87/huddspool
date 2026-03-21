<?php

namespace App\Observers;

use App\Models\Result;
use App\Support\LeagueResultSubmissionMailer;

class ResultObserver
{
    public function saved(Result $result): void
    {
        (new LeagueResultSubmissionMailer)->sendIfNeeded($result);
    }
}
