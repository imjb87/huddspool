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
