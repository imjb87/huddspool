<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Support\SupportTicketService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSupportTicket extends EditRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $actor = Filament::auth()->user();

        abort_unless($actor instanceof User, 403);
        abort_unless($record instanceof SupportTicket, 404);

        return app(SupportTicketService::class)->update($record, $data, $actor);
    }
}
