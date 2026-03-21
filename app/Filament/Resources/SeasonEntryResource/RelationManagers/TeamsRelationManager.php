<?php

namespace App\Filament\Resources\SeasonEntryResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    protected static ?string $title = 'Team registrations';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team_name')
                    ->label('Team')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_telephone')
                    ->label('Telephone'),
                Tables\Columns\TextColumn::make('ruleset.name')
                    ->label('First ruleset'),
                Tables\Columns\TextColumn::make('secondRuleset.name')
                    ->label('Second ruleset')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('venue_name')
                    ->label('Venue')
                    ->wrap(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('GBP'),
            ])
            ->actions([])
            ->headerActions([]);
    }
}
