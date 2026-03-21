<?php

namespace App\Filament\Resources\ExpulsionResource\Pages;

use App\Filament\Resources\ExpulsionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpulsion extends CreateRecord
{
    protected static string $resource = ExpulsionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['season_id'] = $this->getParentRecord()->getKey();

        return $data;
    }
}
