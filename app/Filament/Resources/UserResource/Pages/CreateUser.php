<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\RoleName;
use App\Filament\Resources\UserResource;
use App\Support\SiteAuthorization;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $role = RoleName::from((string) ($data['site_role'] ?? RoleName::Player->value));
        unset($data['site_role']);

        return SiteAuthorization::applyLegacyColumnsForRole($data, $role);
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var Model $record */
        $record = static::getModel()::create($data);

        SiteAuthorization::syncSpatieRoleFromLegacyColumns($record);

        return $record;
    }
}
