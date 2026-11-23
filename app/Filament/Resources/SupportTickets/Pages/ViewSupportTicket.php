<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Domain\Support\SupportTicketService;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reply')
                ->label('Reply')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->form([
                    Forms\Components\Textarea::make('content')
                        ->label('Message')
                        ->required()
                        ->rows(6),
                    Forms\Components\Toggle::make('is_internal_note')
                        ->label('Internal note')
                        ->helperText('Internal notes are visible only to administrators.'),
                ])
                ->action(function (SupportTicket $record, array $data): void {
                    $actor = Filament::auth()->user();

                    abort_unless($actor instanceof User, 403);

                    app(SupportTicketService::class)->addReply(
                        $record,
                        $actor,
                        $data['content'],
                        (bool) ($data['is_internal_note'] ?? false),
                    );

                    $this->record->refresh();

                    Notification::make()
                        ->title('Reply added')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
