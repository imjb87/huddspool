<?php

namespace App\Filament\Resources;

use daacreators\CreatorsTicketing\Filament\Resources\Tickets\TicketResource as BaseTicketResource;
use daacreators\CreatorsTicketing\Models\Ticket;
use EslamRedaDiv\FilamentCopilot\Contracts\CopilotResource;

class SupportTicketResource extends BaseTicketResource implements CopilotResource
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

    public static function copilotResourceDescription(): ?string
    {
        return 'Manage support tickets, ticket status, and unresolved support workload.';
    }

    public static function copilotTools(): array
    {
        return [];
    }
}
