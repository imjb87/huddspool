<?php

namespace App\Filament\Resources\RulesetResource\Pages;

use App\Filament\Resources\RulesetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRuleset extends EditRecord
{
    protected static string $resource = RulesetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
