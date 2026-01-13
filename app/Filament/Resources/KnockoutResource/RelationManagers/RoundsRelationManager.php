<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use Filament\Actions;
use App\KnockoutType;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RoundsRelationManager extends RelationManager
{
    protected static string $relationship = 'rounds';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('position')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->required(),
            Forms\Components\DateTimePicker::make('scheduled_for')
                ->seconds(false)
                ->default(function ($record) {
                    if ($record?->scheduled_for) {
                        return $record->scheduled_for;
                    }

                    return now()->setTime(20, 15);
                }),
            Forms\Components\TextInput::make('best_of')
                ->label('Best of (frames)')
                ->numeric()
                ->minValue(1)
                ->helperText('Leave blank to inherit the knockout best-of. Team knockouts are fixed at 10 frames.')
                ->hidden(fn () => $this->getOwnerRecord()->type === KnockoutType::Team),
            Forms\Components\Toggle::make('is_visible')
                ->label('Visible to users')
                ->helperText('If off, this round will be hidden from the public knockout view.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('position')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('scheduled_for')->dateTime('D j M, g:ia'),
                Tables\Columns\TextColumn::make('best_of_display')
                    ->label('Best of')
                    ->state(fn ($record) => $record->bestOfValue())
                    ->suffix(' frames'),
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('Visible')
                    ->sortable(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('position');
    }

}
