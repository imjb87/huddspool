<?php

declare(strict_types=1);

namespace App\Filament\Resources\FixtureResource\CopilotTools;

use App\Filament\Resources\FixtureResource;
use App\Support\Copilot\Tools\Resources\ListResourceRecordsTool;

class ListFixturesTool extends ListResourceRecordsTool
{
    protected static function resourceClass(): string
    {
        return FixtureResource::class;
    }

    protected function searchableColumns(): array
    {
        return ['homeTeam.name', 'awayTeam.name', 'section.name', 'season.name', 'week'];
    }

    protected function displayColumns(): array
    {
        return ['fixture_date', 'week', 'homeTeam.name', 'awayTeam.name', 'section.name', 'season.name', 'venue.name'];
    }
}
