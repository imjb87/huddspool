<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Models\Result;
use App\Models\Team;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';

    protected static ?string $title = 'Matches Played';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fixture_date')
                    ->label('Date')
                    ->state(function (Result $record): string {
                        return $record->fixture?->fixture_date?->format('d M Y') ?? 'TBC';
                    }),
                Tables\Columns\TextColumn::make('opponent')
                    ->label('Opposing team')
                    ->state(function (Result $record): string {
                        $team = $this->getOwnerRecord();

                        if (! $team instanceof Team) {
                            return $record->away_team_name ?: $record->home_team_name ?: 'Unknown';
                        }

                        if ((int) $record->home_team_id === (int) $team->id) {
                            return $record->away_team_name ?: 'Unknown opponent';
                        }

                        return $record->home_team_name ?: 'Unknown opponent';
                    }),
                Tables\Columns\TextColumn::make('season')
                    ->label('Season')
                    ->state(function (Result $record): string {
                        return $record->fixture?->season?->name
                            ?? $record->section?->season?->name
                            ?? 'Unknown';
                    }),
                Tables\Columns\TextColumn::make('section')
                    ->label('Section')
                    ->state(function (Result $record): string {
                        return $record->section?->name ?? 'Unknown';
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->state(function (Result $record): string {
                        $team = $this->getOwnerRecord();

                        if (! $team instanceof Team) {
                            return sprintf('%s - %s', $record->home_score, $record->away_score);
                        }

                        if ((int) $record->home_team_id === (int) $team->id) {
                            return sprintf('%s - %s', $record->home_score, $record->away_score);
                        }

                        return sprintf('%s - %s', $record->away_score, $record->home_score);
                    }),
            ])
            ->modifyQueryUsing(fn ($query) => $query
                ->select('results.*')
                ->leftJoin('fixtures', 'results.fixture_id', '=', 'fixtures.id')
                ->orderByDesc('fixtures.fixture_date')
                ->with([
                    'fixture.season',
                    'section.season',
                ]))
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
