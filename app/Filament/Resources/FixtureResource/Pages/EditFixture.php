<?php

namespace App\Filament\Resources\FixtureResource\Pages;

use App\Filament\Resources\FixtureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Model;

class EditFixture extends EditRecord
{
    use NestedPage;

    protected static string $resource = FixtureResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if( $record->result ) {
            $record->result->home_team_id = $record->home_team_id;
            $record->result->home_team_name = $record->homeTeam->name;
            $record->result->away_team_id = $record->away_team_id;
            $record->result->away_team_name = $record->awayTeam->name;
            $record->result->submitted_by = auth()->id();            
            $record->result->save();
        }

        return $record;
    }
}
