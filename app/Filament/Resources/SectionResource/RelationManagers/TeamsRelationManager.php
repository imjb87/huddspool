<?php

namespace App\Filament\Resources\SectionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                Tables\Columns\TextColumn::make('row')->label(false)->rowIndex()
                    ->formatStateUsing(fn(string $state): string => $state == 10 ? '0' : $state),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('deducted'),
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
                    ->label('Remove team')
                    ->visible(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->results->count() == 0),
                Tables\Actions\Action::make('DeductPoints')
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
                    ->action(function (array $data, Model $record): void {
                        $record->deducted = $record->deducted + $data['deducted'];
                        $record->save();
                    })
                    ->color('warning')
                    ->icon('heroicon-o-arrow-down'),
                Tables\Actions\Action::make('Withdraw')
                    ->label('Withdraw')
                    ->visible(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->results->count() > 0)
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (RelationManager $livewire, Model $record): void {
                        $week = 0;

                        foreach ($livewire->getOwnerRecord()->season->dates as $date) {
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
                    }),

            ])
            ->paginated(false)
            ->defaultSort('sort')
            ->reorderable('sort');
    }
}
