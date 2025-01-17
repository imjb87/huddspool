<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonResource\Pages;
use App\Filament\Resources\SeasonResource\RelationManagers;
use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;

class SeasonResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Season::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Competitions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                        // Main section spanning 2 columns
                        Forms\Components\Section::make('Season information')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->placeholder('Season name'),

                                Forms\Components\Select::make('is_open')
                                    ->label('Is open')
                                    ->options([
                                        0 => 'No',
                                        1 => 'Yes',
                                    ])
                                    ->required(),
                            ]),

                        // Dates on the right in a smaller section spanning 1 column
                        Forms\Components\Section::make('Schedule')
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
                                    ->maxItems(18),
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
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Is Open?')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit' => Pages\EditSeason::route('/{record}/edit'),

            'sections.create' => Pages\CreateSeasonSection::route('/{record}/sections/create'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }    

    public static function getBreadcrumbRecordLabel($record): string
    {
        return $record->name;
    }
}
