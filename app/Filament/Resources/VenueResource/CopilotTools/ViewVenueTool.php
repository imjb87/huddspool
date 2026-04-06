<?php

declare(strict_types=1);

namespace App\Filament\Resources\VenueResource\CopilotTools;

use App\Filament\Resources\VenueResource;
use App\Support\Copilot\Tools\Resources\ViewResourceRecordTool;

class ViewVenueTool extends ViewResourceRecordTool
{
    protected static function resourceClass(): string
    {
        return VenueResource::class;
    }

    protected function searchableColumns(): array
    {
        return ['name', 'address', 'telephone'];
    }

    protected function displayColumns(): array
    {
        return ['name', 'address', 'telephone', 'latitude', 'longitude'];
    }
}
