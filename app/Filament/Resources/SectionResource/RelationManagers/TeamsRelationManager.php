<?php

namespace App\Filament\Resources\SectionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TeamsRelationManager extends RelationManager
{
    protected static ?string $title = 'Teams';

    protected static string $relationship = 'teams';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\Layout\Grid::make([
                    'lg' => 12,
                ])->schema([
                    Tables\Columns\TextColumn::make('sort')->label(false),
                    Tables\Columns\TextColumn::make('name')->columnSpan(11)
                ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                ->label('Add an existing team'),
                Tables\Actions\CreateAction::make()
                ->label('Create a new team')
                ->slideOver(true)
                ->modalHeading('Create a new team')
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                ->label('Remove team'),
            ])
            ->paginated(false)
            ->defaultSort('sort')
            ->reorderable('sort');
    }
}
