<?php

namespace App\Filament\Resources\RoundResource\Pages;

use App\Filament\Resources\RoundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditRound extends EditRecord
{
    use NestedPage;
    
    protected static string $resource = RoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
