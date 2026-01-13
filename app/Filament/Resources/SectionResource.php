<?php

namespace App\Filament\Resources;

use Filament\Actions;
use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Models\Section;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Season;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static ?string $parentResource = SeasonResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Section information')
                ->columnSpanFull()
                    ->description('Enter the basic information for the section.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Section name'),
                        Forms\Components\Select::make('ruleset_id')
                            ->label('Ruleset')
                            ->relationship('ruleset', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('season_id')
                            ->label('Season')
                            ->relationship('season', 'name')
                            ->default(fn ($livewire) => method_exists($livewire, 'getParentRecord')
                                ? $livewire->getParentRecord()?->getKey()
                                : Season::query()
                                    ->where('slug', request()->route('record'))
                                    ->first()?->id)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            'teams' => RelationManagers\TeamsRelationManager::class,
            'fixtures' => RelationManagers\FixturesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }

}
