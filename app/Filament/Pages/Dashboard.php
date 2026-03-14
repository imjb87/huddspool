<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestResults;
use App\Filament\Widgets\OutstandingFixtures;
use App\Filament\Widgets\SeasonStatsChart;
use App\Filament\Widgets\UserStatsOverview;
use daacreators\CreatorsTicketing\Filament\Widgets\TicketStatsWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getColumns(): int|array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            UserStatsOverview::class,
            SeasonStatsChart::class,
            TicketStatsWidget::class,
            OutstandingFixtures::class,
            LatestResults::class,
        ];
    }
}
