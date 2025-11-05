<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Result;
use Illuminate\Database\Eloquent\Model;

class LatestResults extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Result::query()
                    ->where('is_confirmed', true)
                    ->whereHas('fixture.season', function ($query) {
                        $query->where('is_open', true);
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('home_team_name')->label('Home team')->alignRight()->searchable(),
                Tables\Columns\TextColumn::make('score')->label(false)->alignCenter()->state(function (Model $record) {
                    return $record->home_score . ' - ' . $record->away_score;
                }),
                Tables\Columns\TextColumn::make('away_team_name')->label('Away team')->alignLeft()->searchable(),
            ])
            ->paginated(5)
            ->searchable(false)
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('No results yet');
    }
}
