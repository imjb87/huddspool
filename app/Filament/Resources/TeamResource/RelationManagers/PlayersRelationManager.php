<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlayersRelationManager extends RelationManager
{
    protected static string $relationship = 'players';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique('users', 'email')
                    ->maxLength(255),
                // Role
                Forms\Components\Select::make('role')
                    ->options([
                        1 => 'Player',
                        2 => 'Team admin',
                    ])
                    ->required()
                    ->default(1),
                // Telephone
                Forms\Components\TextInput::make('telephone')
                    ->tel()
                    ->maxLength(255),
                // Is admin
                Forms\Components\Select::make('is_admin')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])
                    ->required()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->label(false)
                    ->color(fn (string $state): string => $state === '1' ? 'success' : 'info')
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Player' : 'Team admin'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
