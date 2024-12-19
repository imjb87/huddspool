<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Models\Section;
use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Competitions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('Section name'),
                Forms\Components\Select::make('season_id')
                    ->label('Season')
                    ->relationship('season', 'name')
                    ->placeholder('Select a season')
                    ->required(),
                Forms\Components\Select::make('ruleset_id')
                    ->label('Ruleset')
                    ->relationship('ruleset', 'name')
                    ->placeholder('Select a ruleset')
                    ->required(),
                Forms\Components\Repeater::make('sectionTeams')
                    ->relationship()
                    ->simple(
                        Forms\Components\Select::make('team_id')
                            ->label('Team')
                            ->relationship('team', 'name')
                            ->placeholder('Select a team')
                            ->required(),
                    )
                    ->columnSpan(2)
                    ->reorderable()
                    ->minItems(10)
                    ->maxItems(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentSeason = Season::latest('id')->first();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruleset.name')
                    ->label('Ruleset')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No ruleset'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('season_id')
                    ->label('Season')
                    ->options(Season::pluck('name', 'id')->toArray())
                    ->default($currentSeason->id),
            ])
            ->actions([
                Tables\Actions\Action::make('fixtures')
                    ->label('View Fixtures')
                    ->icon('heroicon-o-calendar')
                    ->url(fn (Section $section) => route('filament.cp.resources.sections.fixtures', $section)),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false)
            ->searchable(false)
            ->defaultSort('id', 'asc');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            'fixtures' => Pages\SectionFixtures::route('/{record}/fixtures'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}
