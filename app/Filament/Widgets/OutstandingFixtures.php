<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\App;
use App\Models\Fixture;
use Illuminate\Database\Eloquent\Model;

class OutstandingFixtures extends BaseWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // get all fixtures with a null result for the current is_open season
                Fixture::whereDoesntHave('result') // Exclude fixtures with a result
                    ->whereHas('season', function ($query) {
                        $query->where('is_open', true); // Include only seasons with is_open = true
                    })->where('home_team_id', '!=', 1) // Exclude fixtures with home_team_id = 0
                    ->where('away_team_id', '!=', 1) // Exclude fixtures with away_team_id = 0

            )
            ->columns([
                Tables\Columns\TextColumn::make('homeTeam.name')->label('Home team')->alignRight()->searchable(),
                Tables\Columns\TextColumn::make('fixture_date')->label(false)->state(function (Model $record) {
                    return $record->result ? $record->result->home_score . ' - ' . $record->result->away_score : $record->fixture_date->format('d/m');
                })->alignCenter(),
                Tables\Columns\TextColumn::make('awayTeam.name')->label('Away team')->alignLeft()->searchable(),                
            ])
            ->recordUrl(
                fn (Model $record): string => route('filament.admin.resources.fixtures.edit', ['record' => $record]),
            )
            ->paginated(5)
            ->searchable(false)
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('No outstanding fixtures');
    }
}
