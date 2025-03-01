<?php

namespace App\Filament\Resources\SeasonResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpulsionsRelationManager extends RelationManager
{
    protected static string $relationship = 'expulsions';

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                MorphToSelect::make('expellable')
                    ->label('Who was expelled?')
                    ->types([
                        MorphToSelect\Type::make(\App\Models\User::class)
                            ->label('Player')
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(\App\Models\Team::class)
                            ->titleAttribute('name'),
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('expellable.name')
            ->columns([
                Tables\Columns\TextColumn::make('expellable.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
