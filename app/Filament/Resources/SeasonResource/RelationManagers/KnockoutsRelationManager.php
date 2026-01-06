<?php

namespace App\Filament\Resources\SeasonResource\RelationManagers;

use App\KnockoutType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class KnockoutsRelationManager extends RelationManager
{
    use NestedRelationManager;

    protected static string $relationship = 'knockouts';

    public function form(Form $form): Form
    {
        return $form->schema([
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }
}
