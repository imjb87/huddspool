<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateSeasonKnockout extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $resource = SeasonResource::class;
    protected static string $relationship = 'knockouts';
}
