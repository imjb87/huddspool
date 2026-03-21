<?php

namespace App\Filament\Resources\SeasonEntryResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KnockoutRegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'knockoutRegistrations';

    protected static ?string $title = 'Knockout registrations';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('knockout.name')
                    ->label('Knockout')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entrant_name')
                    ->label('Entrant')
                    ->wrap(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('GBP'),
            ])
            ->actions([])
            ->headerActions([]);
    }
}
