<?php

namespace App\Filament\Resources\KnockoutResource\RelationManagers;

use App\KnockoutType;
use App\Models\User;
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
            $formatPlayerOption = fn (?User $player) => trim(
                ($player?->name ?? 'Unknown') .
                ($player?->team?->name ? ' (' . $player->team->name . ')' : '')
            );


            $knockout = $livewire->getOwnerRecord();
            $existingParticipantIds = [];
            if ($knockout && $knockout->participants) {
                foreach ($knockout->participants as $participant) {
                    if ($participant->player_one_id) {
                        $existingParticipantIds[] = $participant->player_one_id;
                    }
                    if ($participant->player_two_id) {
                        $existingParticipantIds[] = $participant->player_two_id;
                    }
                }
            }

            $playerOptions = User::with('team')->get()->mapWithKeys(function ($player) use ($formatPlayerOption) {
                return [$player->id => $formatPlayerOption($player)];
            })->toArray();

            // Add TBC option for player_two_id in doubles
            $playerTwoOptions = [null => 'TBC'] + $playerOptions;

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
                    ->options($playerOptions)
                    ->searchable()
                    ->hidden($type === KnockoutType::Team)
                    ->required($type !== KnockoutType::Team)
                    ->reactive()
                    ->rules([
                        function (?string $attribute, $value, $fail) use (&$livewire, $existingParticipantIds, $type) {
                            $data = $livewire->form->getState();
                            if (
                                isset($data['player_one_id'], $data['player_two_id']) &&
                                $data['player_one_id'] && $data['player_two_id'] &&
                                $data['player_one_id'] === $data['player_two_id']
                            ) {
                                $fail('You cannot select the same player twice.');
                            }
                            // For doubles, check if this pair already exists (regardless of order)
                            if ($type === KnockoutType::Doubles && $data['player_one_id'] && $data['player_two_id']) {
                                $editingId = $livewire->record->id ?? null;
                                $alreadyUsed = $livewire->getOwnerRecord()->participants->first(function($p) use ($data, $editingId) {
                                    $ids = [$p->player_one_id, $p->player_two_id];
                                    $inputIds = [$data['player_one_id'], $data['player_two_id']];
                                    sort($ids);
                                    sort($inputIds);
                                    return $ids == $inputIds && $p->id != $editingId;
                                });
                                if ($alreadyUsed) {
                                    $fail('This pair is already a participant in this knockout.');
                                }
                            } else if ($data['player_one_id'] && in_array($data['player_one_id'], $existingParticipantIds)) {
                                // For singles, check if player already exists
                                $editingId = $livewire->record->id ?? null;
                                $alreadyUsed = $livewire->getOwnerRecord()->participants->first(function($p) use ($data, $editingId) {
                                    return ($p->player_one_id == $data['player_one_id'] || $p->player_two_id == $data['player_one_id']) && $p->id != $editingId;
                                });
                                if ($alreadyUsed) {
                                    $fail('This player is already a participant in this knockout.');
                                }
                            }
                        }
                    ]),
                Forms\Components\Select::make('player_two_id')
                    ->label('Player 2')
                    ->options($playerTwoOptions)
                    ->searchable()
                    ->hidden($type !== KnockoutType::Doubles)
                    ->required($type === KnockoutType::Doubles)
                    ->rules([
                        function (?string $attribute, $value, $fail) use (&$livewire, $existingParticipantIds, $type) {
                            $data = $livewire->form->getState();
                            if (
                                isset($data['player_one_id'], $data['player_two_id']) &&
                                $data['player_one_id'] && $data['player_two_id'] &&
                                $data['player_one_id'] === $data['player_two_id']
                            ) {
                                $fail('You cannot select the same player twice.');
                            }
                            // For doubles, check if this pair already exists (regardless of order)
                            if ($type === KnockoutType::Doubles && $data['player_one_id'] && $data['player_two_id']) {
                                $editingId = $livewire->record->id ?? null;
                                $alreadyUsed = $livewire->getOwnerRecord()->participants->first(function($p) use ($data, $editingId) {
                                    $ids = [$p->player_one_id, $p->player_two_id];
                                    $inputIds = [$data['player_one_id'], $data['player_two_id']];
                                    sort($ids);
                                    sort($inputIds);
                                    return $ids == $inputIds && $p->id != $editingId;
                                });
                                if ($alreadyUsed) {
                                    $fail('This pair is already a participant in this knockout.');
                                }
                            } else if ($data['player_two_id'] && in_array($data['player_two_id'], $existingParticipantIds)) {
                                // For singles, check if player already exists
                                $editingId = $livewire->record->id ?? null;
                                $alreadyUsed = $livewire->getOwnerRecord()->participants->first(function($p) use ($data, $editingId) {
                                    return ($p->player_one_id == $data['player_two_id'] || $p->player_two_id == $data['player_two_id']) && $p->id != $editingId;
                                });
                                if ($alreadyUsed) {
                                    $fail('This player is already a participant in this knockout.');
                                }
                            }
                        }
                    ]),
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
