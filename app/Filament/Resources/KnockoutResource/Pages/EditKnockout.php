<?php

namespace App\Filament\Resources\KnockoutResource\Pages;

use App\Filament\Resources\KnockoutResource;
use App\KnockoutType;
use App\Models\Team;
use App\Models\User;
use App\Services\KnockoutBracketBuilder;
use App\Support\KnockoutParticipantSheetParser;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditKnockout extends EditRecord
{
    protected static string $resource = KnockoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getParticipantImportAction(),
            $this->getGenerateBracketAction(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getGenerateBracketAction(): Actions\Action
    {
        return Actions\Action::make('generateBracket')
            ->label('Generate Bracket')
            ->icon('heroicon-o-cube')
            ->color('primary')
            ->requiresConfirmation()
            ->form([
                Forms\Components\Toggle::make('shuffle')
                    ->label('Shuffle participants')
                    ->helperText('Randomly assign seeds before pairing participants (overwrites existing seeds).'),
            ])
            ->action(function (array $data) {
                $builder = new KnockoutBracketBuilder($this->record);

                try {
                    $builder->generate((bool) ($data['shuffle'] ?? false));
                    $this->record->refresh();

                    Notification::make()
                        ->title('Bracket generated successfully.')
                        ->success()
                        ->send();
                } catch (ValidationException $exception) {
                    $this->addError('generateBracket', $exception->getMessage());

                    Notification::make()
                        ->title($exception->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    protected function getParticipantImportAction(): Actions\Action
    {
        return Actions\Action::make('importParticipants')
            ->label('Import participants')
            ->icon('heroicon-o-arrow-down-on-square-stack')
            ->color('secondary')
            ->modalHeading('Import participants')
            ->modalSubmitActionLabel('Import')
            ->form(fn () => $this->getParticipantImportFormSchema())
            ->action(fn (array $data) => $this->handleImportParticipants($data));
    }

    protected function getParticipantImportFormSchema(): array
    {
        return [
            \Filament\Schemas\Components\Section::make('Source data')
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Textarea::make('raw_csv')
                        ->label('CSV data')
                        ->rows(8)
                        ->required()
                        ->reactive()
                        ->helperText('First column optional seed, following columns for participant names.')
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $this->hydrateParticipantsFromCsv($set, $state)),
                    Forms\Components\Toggle::make('replace_existing')
                        ->label('Remove existing participants before import')
                        ->helperText('If enabled, all current participants will be deleted before importing.')
                        ->default(true),
                ])
                ->columns(1),
            \Filament\Schemas\Components\Section::make('Participants')
                ->columnSpanFull()
                ->visible(fn (Get $get) => filled($get('participants')))
                ->schema([
                    Forms\Components\Repeater::make('participants')
                        ->label(false)
                        ->defaultItems(0)
                        ->schema(fn () => $this->getParticipantRepeaterSchema($this->getKnockoutType()))
                        ->disableItemCreation()
                        ->disableItemDeletion()
                        ->disableItemMovement(),
                ]),
        ];
    }

    protected function getParticipantRepeaterSchema(KnockoutType $type): array
    {
        $isTeam = $type === KnockoutType::Team;
        $isDoubles = $type === KnockoutType::Doubles;
        $showsPlayers = ! $isTeam;

        $schema = [
            Forms\Components\TextInput::make('seed')
                ->label('Seed')
                ->numeric()
                ->minValue(1)
                ->placeholder('Auto'),
            Forms\Components\TextInput::make('label')
                ->label('Display name')
                ->maxLength(255)
                ->placeholder(fn () => $type === KnockoutType::Doubles ? 'Player 1 & Player 2' : $type->participantsLabel()),
        ];

        $schema[] = Forms\Components\Select::make('team_id')
            ->label('Team')
            ->options(fn () => Team::query()->orderBy('name')->pluck('name', 'id')->toArray())
            ->searchable()
            ->preload()
            ->visible($isTeam)
            ->required($isTeam);

        $schema[] = $this->makePlayerSelect('player_one_id', $isDoubles ? 'Player 1' : 'Player')
            ->visible($showsPlayers)
            ->required($showsPlayers);

        $schema[] = $this->makePlayerSelect('player_two_id', 'Player 2')
            ->visible($isDoubles)
            ->helperText('Optional. Leave blank to mark as TBC.');

        return $schema;
    }

    protected function makePlayerSelect(string $field, string $label): Forms\Components\Select
    {
        return Forms\Components\Select::make($field)
            ->label($label)
            ->searchable()
            ->getSearchResultsUsing(function (string $search) {
                return User::query()
                    ->with('team')
                    ->where('name', 'like', '%' . $search . '%')
                    ->orderBy('name')
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn (User $user) => [
                        $user->id => $this->formatPlayerLabel($user),
                    ])
                    ->toArray();
            })
            ->getOptionLabelUsing(function ($value) {
                if (! $value) {
                    return null;
                }

                $user = User::query()->with('team')->find($value);

                return $user ? $this->formatPlayerLabel($user) : null;
            });
    }

    protected function hydrateParticipantsFromCsv(Set $set, ?string $state): void
    {
        if (blank($state)) {
            $set('participants', []);
            return;
        }

        $parsed = KnockoutParticipantSheetParser::parse($state);
        $type = $this->getKnockoutType();

        $participants = collect($parsed->rows)
            ->map(function (array $row, int $index) use ($type) {
                $seed = $row['seed'] ?? null;

                $label = match ($type) {
                    KnockoutType::Doubles => trim(collect([$row['primary'], $row['secondary']])->filter()->implode(' & ')),
                    default => (string) ($row['primary'] ?? ''),
                };

                $state = [
                    'seed' => $seed ? max(1, (int) $seed) : null,
                    'label' => $label,
                    'team_id' => null,
                    'player_one_id' => null,
                    'player_two_id' => null,
                ];

                if ($type === KnockoutType::Team) {
                    $state['team_id'] = $this->findTeamByName($row['primary'])?->id;
                } elseif ($type === KnockoutType::Singles) {
                    $state['player_one_id'] = $this->findPlayerByName($row['primary'])?->id;
                } else {
                    $state['player_one_id'] = $this->findPlayerByName($row['primary'])?->id;
                    $state['player_two_id'] = $this->findPlayerByName($row['secondary'])?->id;
                }

                return $state;
            })
            ->values()
            ->all();

        $set('participants', $participants);
    }

    protected function handleImportParticipants(array $data): void
    {
        $participants = $data['participants'] ?? [];

        if (blank($participants)) {
            throw ValidationException::withMessages([
                'raw_csv' => 'No participants were detected in the CSV data.',
            ]);
        }

        $type = $this->getKnockoutType();
        $replaceExisting = (bool) ($data['replace_existing'] ?? false);

        DB::transaction(function () use ($participants, $type, $replaceExisting) {
            if ($replaceExisting) {
                $this->record->participants()->delete();
            }

            foreach ($participants as $index => $participant) {
                $payload = $this->resolveParticipantPayload($participant, $type, $index);
                $this->record->participants()->create($payload);
            }
        });

        $this->record->refresh();

        Notification::make()
            ->title('Participants imported')
            ->body(sprintf(
                '%d participants added to %s.',
                count($participants),
                $this->record->name
            ))
            ->success()
            ->send();
    }

    protected function resolveParticipantPayload(array $participant, KnockoutType $type, int $index): array
    {
        $seed = $participant['seed'] ?? null;

        $nextSeed = $seed && (int) $seed > 0
            ? (int) $seed
            : ($this->record->participants()->max('seed') + 1);

        $payload = [
            'seed' => $nextSeed,
        ];

        $label = trim((string) ($participant['label'] ?? ''));

        if ($label !== '') {
            $payload['label'] = $label;
        }

        if ($type === KnockoutType::Team) {
            $teamId = $participant['team_id'] ?? null;

            if (! $teamId) {
                throw ValidationException::withMessages([
                    "participants.{$index}.team_id" => 'Select a team for this entry.',
                ]);
            }

            $payload['team_id'] = $teamId;
        } elseif ($type === KnockoutType::Singles) {
            $playerId = $participant['player_one_id'] ?? null;

            if (! $playerId) {
                throw ValidationException::withMessages([
                    "participants.{$index}.player_one_id" => 'Select a player for this entry.',
                ]);
            }

            $payload['player_one_id'] = $playerId;
        } else {
            $playerOne = $participant['player_one_id'] ?? null;
            $playerTwo = $participant['player_two_id'] ?? null;

            if (! $playerOne) {
                throw ValidationException::withMessages([
                    "participants.{$index}.player_one_id" => 'Select player 1 for this pairing.',
                ]);
            }

            if ($playerTwo && $playerOne === $playerTwo) {
                throw ValidationException::withMessages([
                    "participants.{$index}.player_two_id" => 'Player 1 and Player 2 must be different.',
                ]);
            }

            $payload['player_one_id'] = $playerOne;

            if ($playerTwo) {
                $payload['player_two_id'] = $playerTwo;
            }
        }

        return $payload;
    }

    protected function getKnockoutType(): KnockoutType
    {
        $type = $this->record?->type;

        if ($type instanceof KnockoutType) {
            return $type;
        }

        if (is_string($type)) {
            return KnockoutType::from($type);
        }

        return KnockoutType::Singles;
    }

    protected function findTeamByName(?string $name): ?Team
    {
        $normalized = trim(mb_strtolower((string) $name));

        if ($normalized === '') {
            return null;
        }

        return Team::query()
            ->where(function ($query) use ($normalized) {
                $query->whereRaw('LOWER(name) = ?', [$normalized])
                    ->orWhereRaw('LOWER(shortname) = ?', [$normalized]);
            })
            ->first();
    }

    protected function findPlayerByName(?string $name): ?User
    {
        $normalized = trim(mb_strtolower((string) $name));

        if ($normalized === '') {
            return null;
        }

        return User::query()
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->first();
    }

    protected function formatPlayerLabel(User $user): string
    {
        $team = $user->team?->name;

        return $team ? sprintf('%s (%s)', $user->name, $team) : $user->name;
    }
}
