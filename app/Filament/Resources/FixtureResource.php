<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FixtureResource\Pages;
use App\Filament\Resources\FixtureResource\RelationManagers;
use App\Models\Fixture;
use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Request;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Model;

class FixtureResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Fixture::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Competitions';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fixture Information')
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
                        Forms\Components\DateTimePicker::make('fixture_date')
                            ->label('Fixture Date')
                            ->required()
                            ->native(false)
                            ->placeholder('Fixture date')
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('venue_id')
                            ->label('Venue')
                            ->relationship('venue', 'name')
                            ->placeholder('Select a venue')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Result')
                    ->relationship('result')
                        ->schema([
                            Forms\Components\Section::make('Frames')
                                ->schema([
                                    Forms\Components\Repeater::make('frames')
                                        ->label(false)
                                        ->relationship('frames')
                                        ->defaultItems(10)
                                        ->maxItems(10)
                                        ->minItems(10)
                                        ->columns(4)
                                        ->deletable(false)
                                        ->schema([
                                            Forms\Components\Select::make('home_player_id')
                                                ->label('Home Player')
                                                ->placeholder('Select a player')
                                                ->options(function (callable $get) {
                                                    // Fetch players from the selected home team
                                                    $homeTeamId = $get('../../../home_team_id');
                                                    return $homeTeamId ? \App\Models\User::where('team_id', $homeTeamId)->pluck('name', 'id') : [];
                                                }),
                                            Forms\Components\TextInput::make('home_score')
                                                ->label('Home Score')
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->maxValue(1),
                                            Forms\Components\TextInput::make('away_score')
                                                ->label('Away Score')
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->maxValue(1),
                                            Forms\Components\Select::make('away_player_id')
                                                ->label('Away Player')
                                                ->placeholder('Select a player')
                                                ->options(function (callable $get) {
                                                    // Fetch players from the selected away team
                                                    $awayTeamId = $get('../../../away_team_id');
                                                    return $awayTeamId ? \App\Models\User::where('team_id', $awayTeamId)->pluck('name', 'id') : [];
                                                }),
                                        ]),
                                ]),
                                Forms\Components\Section::make('Totals')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('home_score')
                                            ->label('Home Total')
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)                
                                            ->required(),
                                        Forms\Components\TextInput::make('away_score')
                                            ->label('Away Total')
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->required(),
                                    ]),                     
                    ])
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
                Tables\Actions\EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->where('section_id', Request::route('record')))
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

    public static function getAncestor() : ?\Guava\FilamentNestedResources\Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return \Guava\FilamentNestedResources\Ancestor::make(
            'fixtures', // Relationship name
            'section', // Inverse relationship name
        );
    }        
}
