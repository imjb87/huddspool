<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Auth\InviteController;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use STS\FilamentImpersonate\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Impersonate::make()
                ->record($this->getRecord())
                ->redirectTo(route('account.show')),
            Actions\DeleteAction::make(),
            Actions\Action::make('invite')
                ->label('Send Invite')
                ->icon('heroicon-o-envelope')
                ->action(function (Model $record) {
                    $inviteController = new InviteController;
                    $inviteController->send($record);
                    Notification::make()
                        ->title('Invite Sent Successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}
