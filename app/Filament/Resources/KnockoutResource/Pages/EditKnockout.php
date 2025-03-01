<?php

namespace App\Filament\Resources\KnockoutResource\Pages;

use App\Filament\Resources\KnockoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditKnockout extends EditRecord
{
    use NestedPage;

    protected static string $resource = KnockoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
