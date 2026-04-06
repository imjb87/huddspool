<?php

declare(strict_types=1);

namespace App\Filament\Resources\SectionResource\CopilotTools;

use App\Filament\Resources\SectionResource;
use App\Support\Copilot\Tools\Resources\ListResourceRecordsTool;

class ListSectionsTool extends ListResourceRecordsTool
{
    protected static function resourceClass(): string
    {
        return SectionResource::class;
    }

    protected function searchableColumns(): array
    {
        return ['name', 'slug', 'season.name', 'ruleset.name'];
    }

    protected function displayColumns(): array
    {
        return ['name', 'slug', 'season.name', 'ruleset.name'];
    }
}
