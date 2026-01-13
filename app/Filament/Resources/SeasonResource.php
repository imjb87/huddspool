<?php

namespace App\Filament\Resources;

use Filament\Actions;
use App\Filament\Resources\SeasonResource\Pages;
use App\Filament\Resources\SeasonResource\RelationManagers;
use App\Models\Season;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'League Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Main section spanning 2 columns
                \Filament\Schemas\Components\Section::make('Season information')
                ->columnSpanFull()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Season name'),
                    ]),

                // Dates on the right in a smaller section spanning 1 column
                \Filament\Schemas\Components\Section::make('Schedule')
                ->columnSpanFull()
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Repeater::make('dates')
                            ->label(false)
                            ->simple(
                                Forms\Components\DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                            )
                            ->reorderable(false)
                            ->grid(6)
                            ->minItems(18)
                            ->maxItems(18)
                            ->defaultItems(18)
                            ->deletable(false),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_open')
                    ->label('Is Open?')
                    ->alignCenter()
                    ->beforeStateUpdated(function ($record, $state) {
                        Season::all()->each(function ($season) use ($record) {
                            if ($season->is_open && $season->id !== $record->id) {
                                $season->update(['is_open' => 0]);
                            }
                        });
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make()->color('warning'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            'sections' => RelationManagers\SectionsRelationManager::class,
            'knockouts' => RelationManagers\KnockoutsRelationManager::class,
            'expulsions' => RelationManagers\ExpulsionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit' => Pages\EditSeason::route('/{record}/edit'),
        ];
    }
}
