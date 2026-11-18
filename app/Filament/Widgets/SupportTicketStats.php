<?php

namespace App\Filament\Widgets;

use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupportTicketStats extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = ['default' => 'full'];

    protected int|array|null $columns = [
        'md' => 2,
        'xl' => 2,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('Open tickets', SupportTicket::query()->whereIn('status', SupportTicketStatus::openValues())->count())
                ->description('Requests awaiting resolution')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),
            Stat::make('Closed tickets', SupportTicket::query()->where('status', SupportTicketStatus::Closed)->count())
                ->description('Resolved support history')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
