<?php

namespace App\Filament\Resources\RulesetResource\Pages;

use App\Filament\Resources\RulesetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRulesets extends ListRecords
{
    protected static string $resource = RulesetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
