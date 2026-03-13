<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Enums\UserRole;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->options(UserRole::options()),
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
                    ->formatStateUsing(fn (string|int|null $state): string => UserRole::labelFor($state))
                    ->badge(),
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
