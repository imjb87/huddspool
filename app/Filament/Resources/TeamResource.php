<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use App\Models\User;
use EslamRedaDiv\FilamentCopilot\Contracts\CopilotResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TeamResource extends Resource implements CopilotResource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'League Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Information')
                    ->columnSpanFull()
                    ->columns(2)
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
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a venue')
                            ->required(),
                        Forms\Components\Select::make('captain_id')
                            ->label('Captain')
                            ->searchable()
                            ->options(fn (?Team $record) => $record
                                ? User::query()
                                    ->where('team_id', $record->getKey())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                                : [])
                            ->placeholder('Select a captain'),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('captain.name')
                    ->label('Captain')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No captain'),
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
                Actions\EditAction::make()->color('warning'),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MatchesRelationManager::class,
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

    public static function copilotResourceDescription(): ?string
    {
        return 'Manage teams, captains, venues, folded status, and linked players and matches.';
    }

    public static function copilotTools(): array
    {
        return [];
    }
}
