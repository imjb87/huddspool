<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('Team name'),
                Forms\Components\TextInput::make('shortname')
                    ->label('Short Name')
                    ->placeholder('Short name'),
                Forms\Components\Select::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name')
                    ->placeholder('Select a venue')
                    ->required(),
                Forms\Components\Select::make('captain_id')
                    ->label('Captain')
                    ->relationship('captain', 'name')
                    ->placeholder('Select a captain')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('venue.name')
                    ->label('Venue')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No venue'),
                Tables\Columns\TextColumn::make('folded_at')
                    ->badge()
                    ->color('gray')
                    ->label(false)
                    ->formatStateUsing(fn (string $state): string => $state ? 'Folded' : ''),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('folded_at')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('foldTeam')
                        ->label('Fold Team')
                        ->icon('heroicon-o-archive-box')
                        ->requiresConfirmation()
                        ->action(fn (Team $team) => $team->update(['folded_at' => now()])),                
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlayersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
