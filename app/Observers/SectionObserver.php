<?php

namespace App\\Observers;

use App\\Models\\Section;
use App\\Support\\SiteSearch\\SearchableRelationRefresher;

class SectionObserver
{
    public function __construct(
        private readonly SearchableRelationRefresher $searchableRelationRefresher,
    ) {
    }

    public function saved(Section $section): void
    {
        $this->searchableRelationRefresher->refreshSection($section);
    }

    public function deleted(Section $section): void
    {
        $this->searchableRelationRefresher->refreshSection($section);
    }

    public function restored(Section $section): void
    {
        $this->searchableRelationRefresher->refreshSection($section);
    }
}
