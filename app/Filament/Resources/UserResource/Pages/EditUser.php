<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\RoleName;
use App\Filament\Resources\UserResource;
use App\Http\Controllers\Auth\InviteController;
use App\Support\SiteAuthorization;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use STS\FilamentImpersonate\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['site_role'] = SiteAuthorization::inferRoleNameFromLegacy(
            $data['role'] !== null ? (string) $data['role'] : null,
            (bool) ($data['is_admin'] ?? false),
        )->value;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $role = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
        unset($data['site_role']);

        return SiteAuthorization::applyLegacyColumnsForRole($data, $role);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        SiteAuthorization::syncSpatieRoleFromLegacyColumns($record);

        return $record;
    }

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
