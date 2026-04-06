<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FixtureResource\CopilotTools\ListFixturesTool;
use App\Filament\Resources\FixtureResource\CopilotTools\SearchFixturesTool;
use App\Filament\Resources\FixtureResource\CopilotTools\ViewFixtureTool;
use App\Filament\Resources\FixtureResource\Pages;
use App\Models\Fixture;
use EslamRedaDiv\FilamentCopilot\Contracts\CopilotResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FixtureResource extends Resource implements CopilotResource
{
    protected static ?string $model = Fixture::class;

    protected static ?string $parentResource = SectionResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Competitions';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Fixture Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('home_team_id')
                            ->label('Home Team')
                            ->relationship('homeTeam', 'name')
                            ->placeholder('Select a home team')
                            ->disabled(),
                        Forms\Components\Select::make('away_team_id')
                            ->label('Away Team')
                            ->relationship('awayTeam', 'name')
                            ->placeholder('Select an away team')
                            ->disabled(),
                        Forms\Components\Select::make('section_id')
                            ->label('Section')
                            ->relationship('section', 'name')
                            ->placeholder('Select a section')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('fixture_date')
                            ->label('Fixture Date')
                            ->required()
                            ->native(false)
                            ->placeholder('Fixture date')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('venue_id')
                            ->label('Venue')
                            ->relationship('venue', 'name')
                            ->placeholder('Select a venue')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fixture_date')
                    ->dateTime('d/m/Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('homeTeam.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('awayTeam.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('season.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Actions\EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => ! $record->hasRecordedResults(),
            )
            ->defaultSort('week', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFixtures::route('/'),
            'edit' => Pages\EditFixture::route('/{record}/edit'),
        ];
    }

    public static function copilotResourceDescription(): ?string
    {
        return 'Manage league fixtures, fixture dates, venues, and section schedules.';
    }

    public static function copilotTools(): array
    {
        return [
            new ListFixturesTool,
            new ViewFixtureTool,
            new SearchFixturesTool,
        ];
    }
}
