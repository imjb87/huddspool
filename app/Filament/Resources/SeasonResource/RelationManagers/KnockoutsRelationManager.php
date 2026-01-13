<?php

namespace App\Filament\Resources\SeasonResource\RelationManagers;

use Filament\Actions;
use App\Filament\Resources\KnockoutResource;
use App\KnockoutType;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KnockoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'knockouts';

    protected static ?string $relatedResource = KnockoutResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options(KnockoutType::class)
                ->required()
                ->reactive(),
            Forms\Components\TextInput::make('best_of')
                ->numeric()
                ->minValue(1)
                ->visible(fn (Get $get) => in_array($get('type'), [
                    KnockoutType::Singles->value,
                    KnockoutType::Doubles->value,
                ])),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(function ($state) {
                        if ($state instanceof KnockoutType) {
                            return $state->getLabel();
                        }

                        return KnockoutType::from($state)->getLabel();
                    }),
                Tables\Columns\TextColumn::make('best_of')
                    ->label('Best Of')
                    ->placeholder('-'),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }
}
