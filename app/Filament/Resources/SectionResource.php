<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Models\Section;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false)
            ->searchable(false)
            ->defaultGroup(
                Tables\Grouping\Group::make('season.name')
                    ->orderQueryUsing(fn (Builder $query) => $query->orderBy('season_id', 'desc'))
                    ->collapsible(),
            );      
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
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}
