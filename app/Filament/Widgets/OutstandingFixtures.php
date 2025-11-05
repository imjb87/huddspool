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
                Fixture::query()
                    ->with('result')
                    ->whereDoesntHave('result', function ($query) {
                        $query->where('is_confirmed', true);
                    })
                    ->whereHas('season', function ($query) {
                        $query->where('is_open', true);
                    })
                    ->where('home_team_id', '!=', 1)
                    ->where('away_team_id', '!=', 1)
                    ->where('fixture_date', '<', now())
                    ->orderBy('fixture_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('homeTeam.name')->label('Home team')->alignRight()->searchable(),
                Tables\Columns\TextColumn::make('fixture_status')->label(false)->state(function (Model $record) {
                    if ($record->result && ! $record->result->is_confirmed) {
                        return $record->result->home_score . ' - ' . $record->result->away_score;
                    }

                    return $record->fixture_date->format('d/m');
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
