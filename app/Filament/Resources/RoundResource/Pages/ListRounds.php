<?php

namespace App\Filament\Resources\RoundResource\Pages;

use App\Filament\Resources\RoundResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListRounds extends ListRecords
{
    use NestedPage;
    
    protected static string $resource = RoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
