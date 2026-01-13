<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Frame;
use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FramesRelationManager extends RelationManager
{
    protected static string $relationship = 'frames';

    protected static ?string $title = 'Previously played frames';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fixture_date')
                    ->label('Fixture date')
                    ->state(function (Frame $record): string {
                        return $record->result?->fixture?->fixture_date?->format('d M Y') ?? 'TBC';
                    }),
                Tables\Columns\TextColumn::make('opponent')
                    ->label('Opponent')
                    ->state(function (Frame $record): string {
                        $user = $this->getOwnerRecord();

                        if (! $user instanceof User) {
                            return $record->awayPlayer?->name ?? $record->homePlayer?->name ?? 'Unknown';
                        }

                        if ((int) $record->home_player_id === (int) $user->id) {
                            return $record->awayPlayer?->name ?? 'Unknown opponent';
                        }

                        return $record->homePlayer?->name ?? 'Unknown opponent';
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->state(function (Frame $record): string {
                        return sprintf('%s - %s', $record->home_score, $record->away_score);
                    }),
            ])
            ->modifyQueryUsing(fn ($query) => $query
                ->latest('id')
                ->with([
                    'result.fixture',
                    'homePlayer',
                    'awayPlayer',
                ])
                ->limit(10))
            ->headerActions([])
            ->actions([])
            ->bulkActions([])
            ->paginated(false);
    }
}
