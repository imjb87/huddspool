<?php

namespace App\Filament\Resources\FixtureResource\Pages;

use App\Filament\Resources\FixtureResource;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\User;
use Filament\Support\Enums\MaxWidth;

class EditFixture extends EditRecord
{
    use NestedPage;

    protected static string $resource = FixtureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enterOrEditResult')
                ->label(fn() => $this->record->result ? 'Edit Result' : 'Enter Result')
                ->icon(fn() => $this->record->result ? 'heroicon-o-pencil' : 'heroicon-o-pencil-square')
                ->modalHeading('Result & Frames')
                ->modalSubmitActionLabel(fn() => $this->record->result ? 'Update Result' : 'Save Result')
                ->form([
                    Forms\Components\Section::make('Result Totals')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('home_score')
                                ->label('Home Total')
                                ->numeric()
                                ->maxValue(10)
                                ->required(),

                            Forms\Components\TextInput::make('away_score')
                                ->label('Away Total')
                                ->numeric()
                                ->maxValue(10)
                                ->required(),
                        ]),

                    Forms\Components\Section::make('Frames')
                        ->schema([
                            Forms\Components\Repeater::make('frames')
                                ->label('Frames')
                                ->defaultItems(0)
                                ->maxItems(10)
                                ->columns(4)
                                ->columnSpanFull()
                                ->schema([
                                    Forms\Components\Select::make('home_player_id')
                                        ->label('Home Player')
                                        ->options(
                                            fn() =>
                                            [0 => 'Awarded']
                                                + User::where('team_id', $this->record->home_team_id)
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                                ->toArray()
                                        )
                                        ->searchable()
                                        ->required(),

                                    Forms\Components\TextInput::make('home_score')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(1)
                                        ->default(0)
                                        ->required(),

                                    Forms\Components\TextInput::make('away_score')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(1)
                                        ->default(0)
                                        ->required(),

                                    Forms\Components\Select::make('away_player_id')
                                        ->label('Away Player')
                                        ->options(
                                            fn() =>
                                            [0 => 'Awarded']
                                                + User::where('team_id', $this->record->away_team_id)
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                                ->toArray()
                                        )
                                        ->searchable()
                                        ->required(),
                                ]),
                        ]),
                ])
                ->fillForm(function (): array {
                    $result = $this->record->result;

                    if (! $result) {
                        return []; // new entry
                    }

                    return [
                        'home_score' => $result->home_score,
                        'away_score' => $result->away_score,
                        'frames' => $result->frames
                            ->map(fn($f) => [
                                'home_player_id' => $f->home_player_id,
                                'home_score'     => $f->home_score,
                                'away_score'     => $f->away_score,
                                'away_player_id' => $f->away_player_id,
                            ])
                            ->values()
                            ->all(),
                    ];
                })
                ->action(function (array $data) {
                    $existing = $this->record->result;

                    if ($existing) {
                        // Update existing result
                        $existing->update([
                            'home_score'    => $data['home_score'],
                            'away_score'    => $data['away_score'],
                            'submitted_by'  => auth()->id(),
                            'is_confirmed'  => true,
                        ]);

                        // Nuke & repopulate frames (simple + reliable)
                        $existing->frames()->delete();
                        foreach ($data['frames'] ?? [] as $frame) {
                            $existing->frames()->create([
                                'home_player_id' => $frame['home_player_id'],
                                'away_player_id' => $frame['away_player_id'],
                                'home_score'     => $frame['home_score'],
                                'away_score'     => $frame['away_score'],
                            ]);
                        }

                        Notification::make()
                            ->title('Result & frames updated')
                            ->success()
                            ->send();

                        return;
                    }

                    // Create new result
                    $result = $this->record->result()->create([
                        'home_score'     => $data['home_score'],
                        'away_score'     => $data['away_score'],
                        'home_team_id'   => $this->record->home_team_id,
                        'away_team_id'   => $this->record->away_team_id,
                        'section_id'     => $this->record->section_id,
                        'home_team_name' => $this->record->homeTeam->name,
                        'away_team_name' => $this->record->awayTeam->name,
                        'submitted_by'   => auth()->id(),
                        'is_confirmed'   => true,
                    ]);

                    foreach ($data['frames'] ?? [] as $frame) {
                        $result->frames()->create([
                            'home_player_id' => $frame['home_player_id'],
                            'away_player_id' => $frame['away_player_id'],
                            'home_score'     => $frame['home_score'],
                            'away_score'     => $frame['away_score'],
                        ]);
                    }

                    Notification::make()
                        ->title('Result & frames saved')
                        ->success()
                        ->send();
                })
                ->modalWidth(MaxWidth::SixExtraLarge),
        ];
    }
}
