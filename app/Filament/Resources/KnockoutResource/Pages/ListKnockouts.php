<?php

namespace App\Filament\Resources\KnockoutResource\Pages;

use App\Filament\Resources\KnockoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListKnockouts extends ListRecords
{
    use NestedPage;
    
    protected static string $resource = KnockoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
