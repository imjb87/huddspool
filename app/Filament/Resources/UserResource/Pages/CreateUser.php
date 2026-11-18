<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\RoleName;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\SiteAuthorization;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?RoleName $selectedRole = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->selectedRole = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
        unset($data['site_role']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var User $record */
        $record = static::getModel()::create($data);

        SiteAuthorization::assignRole($record, $this->selectedRole ?? RoleName::Player);

        return $record;
    }
}
