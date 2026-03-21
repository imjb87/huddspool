<?php

namespace App\Filament\Resources\ExpulsionResource\Pages;

use App\Filament\Resources\ExpulsionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpulsions extends ListRecords
{
    protected static string $resource = ExpulsionResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Expulsions';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
