<?php

namespace App\\Observers;

use App\\Models\\Ruleset;
use App\\Support\\SiteSearch\\SearchableRelationRefresher;

class RulesetObserver
{
    public function __construct(
        private readonly SearchableRelationRefresher $searchableRelationRefresher,
    ) {
    }

    public function saved(Ruleset $ruleset): void
    {
        $this->searchableRelationRefresher->refreshRuleset($ruleset);
    }

    public function deleted(Ruleset $ruleset): void
    {
        $this->searchableRelationRefresher->refreshRuleset($ruleset);
    }
}
