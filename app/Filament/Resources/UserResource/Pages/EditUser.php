<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('invite')
            ->label('Send Invite')
            ->icon('heroicon-o-envelope')
            ->action(function (Model $record) {
                $inviteController = new \App\Http\Controllers\Auth\InviteController();
                $inviteController->send($record);
                \Filament\Notifications\Notification::make()
                    ->title('Invite Sent Successfully')
                    ->success()
                    ->send();
            }),
        ];
    }

    protected function getActions(): array
    {
        return [
            Impersonate::make()->record($this->getRecord()) // <--
        ];
    }    
}
