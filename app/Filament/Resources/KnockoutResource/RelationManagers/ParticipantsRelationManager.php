<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use App\KnockoutType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager as Manager;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    public function form(Form $form): Form
    {
        return $form->schema(function (Manager $livewire) {
            $type = $livewire->getOwnerRecord()->type;

            return [
                Forms\Components\TextInput::make('label')
                    ->maxLength(255)
                    ->helperText('Optional custom name displayed in brackets.'),
                Forms\Components\TextInput::make('seed')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Lower seeds are placed earlier when generating brackets.'),
                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'name')
                    ->searchable()
                    ->hidden($type !== KnockoutType::Team)
                    ->required($type === KnockoutType::Team),
                Forms\Components\Select::make('player_one_id')
                    ->label($type === KnockoutType::Doubles ? 'Player 1' : 'Player')
                    ->relationship('playerOne', 'name')
                    ->searchable()
                    ->hidden($type === KnockoutType::Team)
                    ->required($type !== KnockoutType::Team),
                Forms\Components\Select::make('player_two_id')
                    ->label('Player 2')
                    ->relationship('playerTwo', 'name')
                    ->searchable()
                    ->hidden($type !== KnockoutType::Doubles)
                    ->required($type === KnockoutType::Doubles),
            ];
        });
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('display_name')
            ->columns([
                Tables\Columns\TextColumn::make('seed')->label('Seed')->sortable(),
                Tables\Columns\TextColumn::make('display_name')->label('Name')->searchable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('seed');
    }
}
