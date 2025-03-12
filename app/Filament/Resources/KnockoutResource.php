<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KnockoutResource\Pages;
use App\Filament\Resources\KnockoutResource\RelationManagers;
use App\Models\Knockout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\KnockoutType;

class KnockoutResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Knockout::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Knockout information')
                    ->description('Enter the basic information for the knockout.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Knockout name'),
                        Forms\Components\Select::make('type')
                            ->options(KnockoutType::class)
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
            'rounds' => RelationManagers\RoundsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnockouts::route('/'),
            'create' => Pages\CreateKnockout::route('/create'),
            'edit' => Pages\EditKnockout::route('/{record}/edit'),
            'rounds.create' => Pages\CreateKnockoutRound::route('/{record}/rounds/create'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return \Guava\FilamentNestedResources\Ancestor::make(
            'knockouts', // Relationship name
            'season', // Inverse relationship name
        );
    }
}
