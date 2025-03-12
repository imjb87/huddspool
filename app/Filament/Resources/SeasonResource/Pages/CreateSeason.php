<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateSeason extends CreateRecord
{
    use NestedPage;
    
    protected static string $resource = SeasonResource::class;
}
