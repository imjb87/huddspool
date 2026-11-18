<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\RoleName;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\SiteAuthorization;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;
use STS\FilamentImpersonate\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?RoleName $selectedRole = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var User $record */
        $record = $this->getRecord();

        $data['site_role'] = $record->roleName()->value;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->selectedRole = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
        unset($data['site_role']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        SiteAuthorization::assignRole($record, $this->selectedRole ?? SiteAuthorization::roleNameFor($record));

        return $record;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord()->refresh();

        if ($record->hasMedia('avatars')) {
            $record->clearLegacyAvatarPath();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Impersonate::make()
                ->record($this->getRecord())
                ->redirectTo(route('account.show')),
            Actions\DeleteAction::make(),
            Actions\Action::make('passwordReset')
                ->label('Send password reset')
                ->icon('heroicon-o-envelope')
                ->requiresConfirmation()
                ->disabled(fn (User $record): bool => blank($record->email))
                ->tooltip(fn (User $record): ?string => blank($record->email)
                    ? 'Add an email address before sending a password reset link.'
                    : null)
                ->action(function (User $record): void {
                    $status = Password::sendResetLink([
                        'email' => $record->email,
                    ]);

                    if ($status !== Password::RESET_LINK_SENT) {
                        Notification::make()
                            ->title('Password reset link not sent')
                            ->body(__($status))
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Password reset link sent')
                        ->body("A password reset link was sent to {$record->email}.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
