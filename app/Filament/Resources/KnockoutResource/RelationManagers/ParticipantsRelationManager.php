<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use App\KnockoutType;
use App\Models\User;
use Closure;
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
            $knockout = $livewire->getOwnerRecord();
            $type = $knockout->type;
            $formatPlayerOption = fn (?User $player) => trim(
                ($player?->name ?? 'Unknown') .
                ($player?->team?->name ? ' (' . $player->team->name . ')' : '')
            );
            $uniqueParticipantRule = function (callable $get) use ($type, $knockout, $livewire) {
                return function (string $attribute, $value, Closure $fail) use ($type, $knockout, $livewire, $get) {
                    if (! $value) {
                        return;
                    }

                    $currentRecordId = $livewire->getMountedTableActionRecord()?->getKey();
                    $participants = $knockout->participants()
                        ->when($currentRecordId, fn ($query) => $query->whereKeyNot($currentRecordId));

                    if ($type === KnockoutType::Team) {
                        if ($participants->where('team_id', $value)->exists()) {
                            $fail('This team is already a participant in this knockout.');
                        }

                        return;
                    }

                    if ($type === KnockoutType::Singles) {
                        if ($participants->where('player_one_id', $value)->exists()) {
                            $fail('This player is already a participant in this knockout.');
                        }

                        return;
                    }

                    if ($type === KnockoutType::Doubles) {
                        $playerId = (int) $value;

                        $exists = $participants
                            ->where(function ($query) use ($playerId) {
                                $query->where('player_one_id', $playerId)
                                    ->orWhere('player_two_id', $playerId);
                            })
                            ->exists();

                        if ($exists) {
                            $fail('This player is already part of a pairing in this knockout.');
                        }
                    }
                };
            };

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
                    ->required($type === KnockoutType::Team)
                    ->rule($uniqueParticipantRule),
                Forms\Components\Select::make('player_one_id')
                    ->label($type === KnockoutType::Doubles ? 'Player 1' : 'Player')
                    ->relationship('playerOne', 'name', fn ($query) => $query->with('team'))
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn (User $player) => $formatPlayerOption($player))
                    ->hidden($type === KnockoutType::Team)
                    ->required($type !== KnockoutType::Team)
                    ->rule($uniqueParticipantRule),
                Forms\Components\Select::make('player_two_id')
                    ->label('Player 2')
                    ->relationship('playerTwo', 'name', fn ($query) => $query->with('team'))
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn (User $player) => $formatPlayerOption($player))
                    ->hidden($type !== KnockoutType::Doubles)
                    ->helperText('Optional. Leave blank to mark as TBC.')
                    ->rule($uniqueParticipantRule),
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
