<?php

declare(strict_types=1);

namespace App\Filament\Resources\TeamResource\CopilotTools;

use App\Filament\Resources\TeamResource;
use App\Support\Copilot\Tools\Resources\ListResourceRecordsTool;

class ListTeamsTool extends ListResourceRecordsTool
{
    protected static function resourceClass(): string
    {
        return TeamResource::class;
    }

    protected function searchableColumns(): array
    {
        return ['name', 'shortname', 'captain.name', 'venue.name'];
    }

    protected function displayColumns(): array
    {
        return ['name', 'shortname', 'captain.name', 'venue.name', 'folded_at'];
    }
}
