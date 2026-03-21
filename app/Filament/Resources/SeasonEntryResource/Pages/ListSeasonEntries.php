<?php

namespace App\Filament\Resources\SeasonEntryResource\Pages;

use App\Filament\Resources\SeasonEntryResource;
use Filament\Resources\Pages\ListRecords;

class ListSeasonEntries extends ListRecords
{
    protected static string $resource = SeasonEntryResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Entries';
    }
}
