<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\FixtureResource;
use App\Models\Fixture;
use Filament\Resources\Pages\ListRecords;

class SectionFixtures extends ListRecords
{
    protected static string $resource = FixtureResource::class;
}
