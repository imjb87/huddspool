<?php

namespace App\Domain\Fixtures;

use App\Domain\Fixtures\Actions\GenerateFixtures;
use App\Models\Section;

class FixtureService
{
    public function __construct(private Section $section) {}

    /**
     * @return array<int, list<array<string, int|string|null>>>
     */
    public function generate(): array
    {
        return app(GenerateFixtures::class)->generate($this->section);
    }
}
