<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\CopilotTools\ListSectionsTool;
use App\Filament\Resources\SectionResource\CopilotTools\SearchSectionsTool;
use App\Filament\Resources\SectionResource\CopilotTools\ViewSectionTool;
use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Models\Season;
use App\Models\Section;
use EslamRedaDiv\FilamentCopilot\Contracts\CopilotResource;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SectionResource extends Resource implements CopilotResource
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruleset.name')
                    ->label('Ruleset')
                    ->badge()
                    ->sortable(),
            ])
            ->recordUrl(fn (Section $record): string => static::getUrl('edit', [
                'record' => $record,
                'season' => $record->season,
            ]))
            ->filters([
                //
            ])
            ->actions([])
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditSection::class,
            Pages\PreviewFixtures::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
            'preview-fixtures' => Pages\PreviewFixtures::route('/{record}/preview-fixtures'),
        ];
    }

    public static function copilotResourceDescription(): ?string
    {
        return 'Manage sections, linked rulesets, teams, and generated fixtures within each season.';
    }

    public static function copilotTools(): array
    {
        return [
            new ListSectionsTool,
            new ViewSectionTool,
            new SearchSectionsTool,
        ];
    }
}
