<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use App\Models\KnockoutParticipant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class MatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'matches';

    public function form(Form $form): Form
    {
        return $form->schema(function (RelationManager $livewire) {
            $knockout = $livewire->getOwnerRecord();

            $participantSelect = function (string $column, string $relationship, string $label) use ($knockout) {
                return Forms\Components\Select::make($column)
                    ->label($label)
                    ->relationship(
                        name: $relationship,
                        titleAttribute: 'label',
                        modifyQueryUsing: fn ($query) => $query->where('knockout_id', $knockout->id)
                    )
                    ->getOptionLabelFromRecordUsing(fn (KnockoutParticipant $record) => $record->display_name)
                    ->searchable();
            };

            return [
                Forms\Components\Hidden::make('knockout_id')
                    ->default($knockout->id),
                Forms\Components\Select::make('knockout_round_id')
                    ->label('Round')
                    ->relationship(
                        name: 'round',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('knockout_id', $knockout->id)
                    )
                    ->required(),
                $participantSelect('home_participant_id', 'homeParticipant', 'Home'),
                $participantSelect('away_participant_id', 'awayParticipant', 'Away'),
                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                Forms\Components\Select::make('venue_id')
                    ->relationship('venue', 'name')
                    ->searchable(),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->seconds(false),
                Forms\Components\TextInput::make('best_of')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Override frames for this match only.'),
                Forms\Components\TextInput::make('home_score')
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('away_score')
                    ->numeric()
                    ->minValue(0),
            ];
        });
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('round.name')->label('Round')->sortable(),
                Tables\Columns\TextColumn::make('position')->sortable(),
                Tables\Columns\TextColumn::make('homeParticipant.display_name')->label('Home'),
                Tables\Columns\TextColumn::make('score')
                    ->state(fn ($record) => $record->home_score !== null && $record->away_score !== null
                        ? "{$record->home_score} - {$record->away_score}"
                        : 'TBC')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('awayParticipant.display_name')->label('Away'),
                Tables\Columns\TextColumn::make('starts_at')->label('Scheduled')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('round')
                    ->relationship(
                        name: 'round',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('knockout_id', $this->getOwnerRecord()->id)
                    )
                    ->label('Round'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->orderBy('knockout_round_id')->orderBy('position'))
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
