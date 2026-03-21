<?php

namespace App\Filament\Resources\ExpulsionResource\Pages;

use App\Filament\Resources\ExpulsionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpulsion extends EditRecord
{
    protected static string $resource = ExpulsionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
