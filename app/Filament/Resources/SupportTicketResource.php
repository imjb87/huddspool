<?php

namespace App\Filament\Resources;

use daacreators\CreatorsTicketing\Filament\Resources\Tickets\TicketResource as BaseTicketResource;
use daacreators\CreatorsTicketing\Models\Ticket;

class SupportTicketResource extends BaseTicketResource
{
    protected static ?string $slug = 'tickets';

    public static function getNavigationBadge(): ?string
    {
        $count = Ticket::whereHas('status', fn ($query) => $query->where('is_closing_status', false))->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
