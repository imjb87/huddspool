<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoundResource\Pages;
use App\Filament\Resources\RoundResource\RelationManagers;
use App\Models\Round;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedResource;

class RoundResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Round::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Round information')
                    ->description('Enter the basic information for the round.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Round name'),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->required(),
                        Forms\Components\Select::make('knockout_id')
                            ->label('Knockout')
                            ->relationship('knockout', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->required(),
                        Forms\Components\TextInput::make('best_of')
                            ->label('Best of')
                            ->required()
                            ->placeholder('Best of'),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            'matches' => RelationManagers\MatchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRounds::route('/'),
            'create' => Pages\CreateRound::route('/create'),
            'edit' => Pages\EditRound::route('/{record}/edit'),
        ];
    }

    public static function getAncestor() : ?\Guava\FilamentNestedResources\Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return \Guava\FilamentNestedResources\Ancestor::make(
            'rounds', // Relationship name
            'knockout', // Inverse relationship name
        );
    }    
}
