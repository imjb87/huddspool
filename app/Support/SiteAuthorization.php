<?php

namespace App\Support;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SiteAuthorization
{
    /**
     * @return list<RoleName>
     */
    public static function roles(): array
    {
        return [
            RoleName::Admin,
            RoleName::TeamAdmin,
            RoleName::Player,
        ];
    }

    /**
     * @return list<PermissionName>
     */
    public static function permissions(): array
    {
        return [
            PermissionName::AccessAdminPanel,
            PermissionName::ViewPulse,
            PermissionName::ImpersonateUsers,
            PermissionName::ManageUsers,
            PermissionName::ManageTeams,
            PermissionName::ManageVenues,
            PermissionName::ManageSeasons,
            PermissionName::ManageSections,
            PermissionName::ManageFixtures,
            PermissionName::ManageRulesets,
            PermissionName::ManageKnockouts,
            PermissionName::ManageNews,
            PermissionName::ManagePages,
            PermissionName::ManageSupportTickets,
            PermissionName::SubmitLeagueResults,
            PermissionName::SubmitKnockoutResults,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function roleOptions(bool $includeAdmin = true): array
    {
        return collect(self::roles())
            ->filter(fn (RoleName $role) => $includeAdmin || $role !== RoleName::Admin)
            ->mapWithKeys(fn (RoleName $role) => [$role->value => $role->label()])
            ->all();
    }

    /**
     * @return array<string, list<string>>
     */
    public static function permissionMap(): array
    {
        return [
            RoleName::Admin->value => [
                PermissionName::AccessAdminPanel->value,
                PermissionName::ViewPulse->value,
                PermissionName::ImpersonateUsers->value,
                PermissionName::ManageUsers->value,
                PermissionName::ManageTeams->value,
                PermissionName::ManageVenues->value,
                PermissionName::ManageSeasons->value,
                PermissionName::ManageSections->value,
                PermissionName::ManageFixtures->value,
                PermissionName::ManageRulesets->value,
                PermissionName::ManageKnockouts->value,
                PermissionName::ManageNews->value,
                PermissionName::ManagePages->value,
                PermissionName::ManageSupportTickets->value,
                PermissionName::SubmitLeagueResults->value,
                PermissionName::SubmitKnockoutResults->value,
            ],
            RoleName::TeamAdmin->value => [
                PermissionName::SubmitLeagueResults->value,
                PermissionName::SubmitKnockoutResults->value,
            ],
            RoleName::Player->value => [],
        ];
    }

    public static function ensureRolesAndPermissionsExist(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::permissions() as $permissionName) {
            Permission::findOrCreate($permissionName->value, 'web');
        }

        foreach (self::roles() as $roleName) {
            $role = Role::findOrCreate($roleName->value, 'web');
            $role->syncPermissions(Arr::get(self::permissionMap(), $roleName->value, []));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public static function inferRoleNameFromLegacy(?string $legacyRoleValue, bool $isAdmin): RoleName
    {
        if ($isAdmin) {
            return RoleName::Admin;
        }

        return $legacyRoleValue === UserRole::TeamAdmin->value
            ? RoleName::TeamAdmin
            : RoleName::Player;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyLegacyColumnsForRole(array $data, RoleName|string $role): array
    {
        $roleName = self::normalizeRoleName($role);

        $data['role'] = self::legacyRoleValueFor($roleName);
        $data['is_admin'] = $roleName === RoleName::Admin;

        return $data;
    }

    public static function syncSpatieRoleFromLegacyColumns(User $user): void
    {
        if (! $user->exists) {
            return;
        }

        $roleName = self::inferRoleNameFromLegacy(
            $user->role !== null ? (string) $user->role : null,
            (bool) $user->is_admin,
        );

        $user->syncRoles([$roleName->value]);
    }

    public static function assignRole(User $user, RoleName|string $role): void
    {
        $roleName = self::normalizeRoleName($role);

        $user->forceFill([
            'role' => self::legacyRoleValueFor($roleName),
            'is_admin' => $roleName === RoleName::Admin,
        ])->saveQuietly();

        $user->syncRoles([$roleName->value]);
    }

    public static function normalizeRoleName(RoleName|string $role): RoleName
    {
        return $role instanceof RoleName ? $role : RoleName::from($role);
    }

    public static function legacyRoleValueFor(RoleName $role): string
    {
        return match ($role) {
            RoleName::Admin, RoleName::Player => UserRole::Player->value,
            RoleName::TeamAdmin => UserRole::TeamAdmin->value,
        };
    }
}
