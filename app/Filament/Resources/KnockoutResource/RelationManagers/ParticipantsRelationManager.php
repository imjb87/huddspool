<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use Filament\Actions;
use App\KnockoutType;
use App\Models\KnockoutParticipant;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager as Manager;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    public function form(Schema $schema): Schema
    {
        return $schema->schema(function (Manager $livewire) {
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
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('seed')->label('Seed')->sortable(),
                Tables\Columns\TextColumn::make('participant_name')
                    ->label('Name')
                    ->state(fn (KnockoutParticipant $record): string => $this->getParticipantName($record))
                    ->searchable(query: function ($query, string $search): void {
                        $query->where(function ($query) use ($search) {
                            $query->whereHas('team', fn ($teamQuery) => $teamQuery->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('playerOne', fn ($playerQuery) => $playerQuery->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('playerTwo', fn ($playerQuery) => $playerQuery->where('name', 'like', "%{$search}%"));
                        });
                    })
                    ->sortable(query: function ($query, string $direction): void {
                        $query->leftJoin('teams', 'knockout_participants.team_id', '=', 'teams.id')
                            ->leftJoin('users as player_one', 'knockout_participants.player_one_id', '=', 'player_one.id')
                            ->leftJoin('users as player_two', 'knockout_participants.player_two_id', '=', 'player_two.id')
                            ->orderByRaw(sprintf(
                                'COALESCE(teams.name, player_one.name, player_two.name, \'\') %s',
                                $direction === 'asc' ? 'asc' : 'desc'
                            ))
                            ->select('knockout_participants.*');
                    }),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('seed');
    }

    private function getParticipantName(KnockoutParticipant $participant): string
    {
        $type = $participant->knockout?->type ?? KnockoutType::Singles;

        return match ($type) {
            KnockoutType::Singles => $participant->playerOne?->name ?? 'TBC',
            KnockoutType::Doubles => $this->formatDoublesName($participant),
            KnockoutType::Team => $participant->team?->name ?? 'TBC',
        };
    }

    private function formatDoublesName(KnockoutParticipant $participant): string
    {
        $playerOne = $participant->playerOne?->name;
        $playerTwo = $participant->playerTwo?->name;

        if (! $playerOne && ! $playerTwo) {
            return 'TBC';
        }

        if ($playerOne && ! $playerTwo) {
            return "{$playerOne} & TBC";
        }

        if (! $playerOne && $playerTwo) {
            return "TBC & {$playerTwo}";
        }

        return "{$playerOne} & {$playerTwo}";
    }
}
