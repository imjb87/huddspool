<?php

namespace App\Filament\Resources\SectionResource\RelationManagers;

use App\Models\SectionTeam;
use App\Models\Team;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TeamsRelationManager extends RelationManager
{
    protected static ?string $title = 'Teams';

    protected static string $relationship = 'sectionTeams';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn (SectionTeam $record): ?string => $record->team?->name)
            ->columns([
                Tables\Columns\TextColumn::make('display_sort')
                    ->label(false),
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Team'),
                Tables\Columns\TextColumn::make('deducted')
                    ->label('Deducted')
                    ->formatStateUsing(function (mixed $state): string {
                        $deducted = (int) $state;

                        if ($deducted <= 0) {
                            return '0 pts';
                        }

                        return sprintf('-%d pts', $deducted);
                    })
                    ->badge()
                    ->color(fn (mixed $state): string => (int) $state > 0 ? 'danger' : 'gray'),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with('team'))
            ->filters([
                //
            ])
            ->headerActions([
                Actions\Action::make('AddExistingTeam')
                    ->label('Add existing teams')
                    ->form([
                        Forms\Components\Select::make('team_ids')
                            ->label('Teams')
                            ->options(fn (RelationManager $livewire): array => $this->selectableTeamOptions($livewire))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (RelationManager $livewire, array $data): void {
                        $nextSort = $this->nextSortFor($livewire);

                        collect($data['team_ids'])
                            ->map(fn (mixed $teamId): int => (int) $teamId)
                            ->each(function (int $teamId) use ($livewire, &$nextSort): void {
                                $livewire->getOwnerRecord()
                                    ->sectionTeams()
                                    ->create([
                                        'team_id' => $teamId,
                                        'sort' => $nextSort,
                                        'deducted' => 0,
                                    ]);

                                $nextSort++;
                            });
                    }),
                Actions\Action::make('CreateTeam')
                    ->label('Create a new team')
                    ->slideOver(true)
                    ->modalHeading('Create a new team')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (RelationManager $livewire, array $data): void {
                        $team = Team::query()->create([
                            'name' => $data['name'],
                        ]);

                        $livewire->getOwnerRecord()
                            ->sectionTeams()
                            ->create([
                                'team_id' => $team->id,
                                'sort' => $this->nextSortFor($livewire),
                                'deducted' => 0,
                            ]);
                    }),
            ])
            ->actions([
                Actions\DeleteAction::make()
                    ->label('Remove team')
                    ->visible(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->results->count() == 0),
                Actions\Action::make('DeductPoints')
                    ->label('Deduct points')
                    ->modalHeading('Deduct points')
                    ->modalDescription('Deduct points from the team')
                    ->form([
                        Forms\Components\TextInput::make('deducted')
                            ->label('Points to deduct')
                            ->required()
                            ->rules('numeric')
                            ->default(0),
                    ])
                    ->fillForm(function (SectionTeam $record): array {
                        return [
                            'deducted' => (int) $record->deducted,
                        ];
                    })
                    ->action(function (SectionTeam $record, array $data): void {
                        $record->deducted = (int) $data['deducted'];
                        $record->save();
                    })
                    ->color('warning')
                    ->icon('heroicon-o-arrow-down'),
                Actions\Action::make('Withdraw')
                    ->label('Withdraw')
                    ->visible(fn (SectionTeam $record): bool => is_null($record->withdrawn_at))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire, SectionTeam $record): void {
                        $byeTeam = Team::byeOrFail();
                        $week = 0;

                        $section = $livewire->getOwnerRecord();

                        $record->withdrawn_at = now();
                        $record->save();

                        foreach ($section->season->dates as $date) {
                            $week++;
                            if ($date > now()) {
                                break;
                            }
                        }

                        if ($week < 9) {
                            $livewire->getOwnerRecord()->results()->each(function ($result) use ($record) {
                                if ($result->home_team_id == $record->team_id || $result->away_team_id == $record->team_id) {
                                    $result->frames()->delete();
                                    $result->delete();
                                }
                            });
                        } else {
                            $livewire->getOwnerRecord()->results()->where('week', '>', 9)->each(function ($result) use ($record) {
                                if ($result->home_team_id == $record->team_id || $result->away_team_id == $record->team_id) {
                                    $result->frames()->delete();
                                    $result->delete();
                                }
                            });
                        }

                        $section->fixtures()->each(function ($fixture) use ($byeTeam, $record) {
                            if ($fixture->home_team_id == $record->team_id) {
                                if (! $fixture->result) {
                                    $fixture->home_team_id = $byeTeam->getKey();
                                    $fixture->save();
                                }
                            }
                            if ($fixture->away_team_id == $record->team_id) {
                                if (! $fixture->result) {
                                    $fixture->away_team_id = $byeTeam->getKey();
                                    $fixture->save();
                                }
                            }
                        });
                    }),

            ])
            ->paginated(false)
            ->defaultSort('sort')
            ->reorderable('sort');
    }

    private function selectableTeamOptions(RelationManager $livewire): array
    {
        $section = $livewire->getOwnerRecord();

        $existingTeamIds = $section->sectionTeams()->pluck('team_id')->all();
        $byeTeamId = Team::query()
            ->where('name', Team::BYE_NAME)
            ->value('id');

        $excludedTeamIds = array_values(array_filter(
            $existingTeamIds,
            fn (int $teamId): bool => $teamId !== $byeTeamId
        ));

        return Team::query()
            ->when($excludedTeamIds !== [], fn ($query) => $query->whereNotIn('id', $excludedTeamIds))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    private function nextSortFor(RelationManager $livewire): int
    {
        return ((int) $livewire->getOwnerRecord()->sectionTeams()->max('sort')) + 1;
    }
}
