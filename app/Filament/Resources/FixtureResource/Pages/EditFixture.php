<?php

namespace App\Filament\Resources\FixtureResource\Pages;

use App\Filament\Resources\FixtureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class EditFixture extends EditRecord
{
    use NestedPage;

    protected static string $resource = FixtureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enterResult')
                ->label('Enter Result')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn() => $this->record->result === null) // hide if already has result
                ->form([
                    Forms\Components\Section::make('Enter Result')
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
                                            fn() => User::where('team_id', $this->record->home_team_id)->pluck('name', 'id')
                                        )
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
                                            fn() => User::where('team_id', $this->record->away_team_id)->pluck('name', 'id')
                                        )
                                        ->required(),
                                ]),
                        ]),
                ])
                ->modalHeading('Enter Result & Frames')
                ->modalSubmitActionLabel('Save Result')
                ->action(function (array $data) {
                    $result = $this->record->result()->create([
                        'home_score' => $data['home_score'],
                        'away_score' => $data['away_score'],
                        'home_team_id' => $this->record->home_team_id,
                        'away_team_id' => $this->record->away_team_id,
                        'section_id' => $this->record->section_id,
                        'home_team_name' => $this->record->homeTeam->name,
                        'away_team_name' => $this->record->awayTeam->name,
                        'submitted_by' => auth()->id(),
                    ]);

                    foreach ($data['frames'] ?? [] as $frame) {
                        $result->frames()->create([
                            'home_player_id' => $frame['home_player_id'],
                            'away_player_id' => $frame['away_player_id'],
                            'home_score' => $frame['home_score'],
                            'away_score' => $frame['away_score'],
                        ]);
                    }

                    Notification::make()
                        ->title('Result & frames saved')
                        ->success()
                        ->send();
                }),
        ];
    }
}
