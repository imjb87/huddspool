<?php

declare(strict_types=1);

namespace App\Filament\Resources\SeasonEntryResource\CopilotTools;

use App\Filament\Resources\SeasonEntryResource;
use App\Support\Copilot\Tools\Resources\SearchResourceRecordsTool;

class SearchSeasonEntriesTool extends SearchResourceRecordsTool
{
    protected static function resourceClass(): string
    {
        return SeasonEntryResource::class;
    }

    protected function searchableColumns(): array
    {
        return ['reference', 'contact_name', 'contact_email', 'contact_telephone', 'venue_name', 'notes'];
    }

    protected function displayColumns(): array
    {
        return ['reference', 'contact_name', 'contact_email', 'contact_telephone', 'venue_name', 'total_amount', 'paid_at'];
    }
}
