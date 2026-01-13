<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlayersRelationManager extends RelationManager
{
    protected static string $relationship = 'players';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                // Role
                Forms\Components\Select::make('role')
                    ->required()
                    ->options([
                        '1' => 'Player',
                        '2' => 'Team Admin',
                    ]),
                Forms\Components\TextInput::make('telephone')
                    ->tel()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('role')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        '1' => 'Player',
                        '2' => 'Team Admin',
                    })->badge()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
                Actions\AssociateAction::make(),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    Actions\DissociateAction::make(),
                ]),
            ])
            ->bulkActions([

            ])
            ->defaultSort('name', 'asc');
    }
}
