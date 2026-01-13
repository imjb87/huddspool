<?php

namespace App\Filament\Resources\SectionResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Models\SectionTeam;

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
            ->columns([
                Tables\Columns\TextColumn::make('row')->label(false)->rowIndex()
                    ->formatStateUsing(fn(string $state): string => $state == 10 ? '0' : $state),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.deducted'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Add an existing team'),
                Actions\CreateAction::make()
                    ->label('Create a new team')
                    ->slideOver(true)
                    ->modalHeading('Create a new team')
            ])
            ->actions([
                Actions\DetachAction::make()
                    ->label('Remove team')
                    ->visible(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->results->count() == 0),
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
                    ->visible(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->results->count() > 0)
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire, Model $record): void {
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

                        $section->fixtures()->each(function ($fixture) use ($record) {
                            if ($fixture->home_team_id == $record->id) {
                                if (!$fixture->result) {
                                    $fixture->home_team_id = 1; // Set to bye team
                                    $fixture->save();
                                }
                            }
                            if ($fixture->away_team_id == $record->id) {
                                if (!$fixture->result) {
                                    $fixture->away_team_id = 1; // Set to bye team
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
}
