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
use Illuminate\Database\Eloquent\Model;

class TeamsRelationManager extends RelationManager
{
    protected static ?string $title = 'Teams';

    protected static string $relationship = 'teams';

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
            ->recordTitleAttribute('name')
            ->allowDuplicates()
            ->columns([
                Tables\Columns\TextColumn::make('row')->label(false)->rowIndex()
                    ->formatStateUsing(fn (string $state): string => (string) SectionTeam::displaySortValue((int) $state)),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('deducted')
                    ->label('Deducted')
                    ->state(fn (Model $record): int => (int) ($record->pivot?->deducted ?? 0))
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
            ->filters([
                //
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Add an existing team')
                    ->recordSelectOptionsQuery(function ($query, RelationManager $livewire) {
                        $section = $livewire->getOwnerRecord();

                        $existingTeamIds = $section->teams()->pluck('teams.id')->all();
                        $byeTeamId = Team::query()
                            ->where('name', Team::BYE_NAME)
                            ->value('id');

                        $excludedTeamIds = array_values(array_filter(
                            $existingTeamIds,
                            fn (int $teamId): bool => $teamId !== $byeTeamId
                        ));

                        if ($excludedTeamIds === []) {
                            return $query;
                        }

                        return $query->whereNotIn('teams.id', $excludedTeamIds);
                    }),
                Actions\CreateAction::make()
                    ->label('Create a new team')
                    ->slideOver(true)
                    ->modalHeading('Create a new team'),
            ])
            ->actions([
                Actions\DetachAction::make()
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
                    ->fillForm(function (RelationManager $livewire, Model $record): array {
                        $pivot = $record->pivot;

                        if (! $pivot) {
                            $section = $livewire->getOwnerRecord();

                            $pivot = SectionTeam::query()
                                ->where('section_id', $section->id)
                                ->where('team_id', $record->id)
                                ->first();
                        }

                        return [
                            'deducted' => (int) ($pivot->deducted ?? 0),
                        ];
                    })
                    ->action(function (RelationManager $livewire, Model $record, array $data): void {
                        $section = $livewire->getOwnerRecord();

                        $pivot = SectionTeam::query()
                            ->where('section_id', $section->id)
                            ->where('team_id', $record->id)
                            ->first();

                        if (! $pivot) {
                            return;
                        }

                        $pivot->deducted = (int) $data['deducted'];
                        $pivot->save();
                    })
                    ->color('warning')
                    ->icon('heroicon-o-arrow-down'),
                Actions\Action::make('Withdraw')
                    ->label('Withdraw')
                    ->visible(function (RelationManager $livewire, Model $record): bool {
                        return is_null($record->pivot?->withdrawn_at ?? null);
                    })
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire, Model $record): void {
                        $byeTeam = Team::byeOrFail();
                        $week = 0;

                        $section = $livewire->getOwnerRecord();

                        $section->teams()->updateExistingPivot($record->id, ['withdrawn_at' => now()]);

                        foreach ($section->season->dates as $date) {
                            $week++;
                            if ($date > now()) {
                                break;
                            }
                        }

                        if ($week < 9) {
                            $livewire->getOwnerRecord()->results()->each(function ($result) use ($record) {
                                if ($result->home_team_id == $record->id || $result->away_team_id == $record->id) {
                                    $result->frames()->delete();
                                    $result->delete();
                                }
                            });
                        } else {
                            $livewire->getOwnerRecord()->results()->where('week', '>', 9)->each(function ($result) use ($record) {
                                if ($result->home_team_id == $record->id || $result->away_team_id == $record->id) {
                                    $result->frames()->delete();
                                    $result->delete();
                                }
                            });
                        }

                        $section->fixtures()->each(function ($fixture) use ($byeTeam, $record) {
                            if ($fixture->home_team_id == $record->id) {
                                if (! $fixture->result) {
                                    $fixture->home_team_id = $byeTeam->getKey();
                                    $fixture->save();
                                }
                            }
                            if ($fixture->away_team_id == $record->id) {
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

    public function getTableRecordKey(Model|array $record): string
    {
        if (is_array($record)) {
            return (string) ($record['id'] ?? '');
        }

        $relationship = $this->getTable()->getRelationship();
        $pivotAccessor = $relationship?->getPivotAccessor();

        $pivot = $pivotAccessor ? $record->getRelationValue($pivotAccessor) : null;

        if ($pivot?->getKey()) {
            return (string) $pivot->getKey();
        }

        return (string) $record->getKey();
    }
}
