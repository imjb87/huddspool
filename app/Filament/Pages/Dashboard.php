<?php
 
namespace App\Filament\Pages;

use App\Filament\Widgets\UserStatsOverview;
use App\Filament\Widgets\OutstandingFixtures;
use App\Filament\Widgets\LatestResults;
use daacreators\CreatorsTicketing\Filament\Widgets\TicketStatsWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getWidgets(): array
    {
        return [
            UserStatsOverview::class,
            TicketStatsWidget::class,
            OutstandingFixtures::class,
            LatestResults::class,
        ];
    }
}
