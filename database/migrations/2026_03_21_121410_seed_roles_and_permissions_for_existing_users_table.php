<?php

use App\Models\User;
use App\Support\SiteAuthorization;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        SiteAuthorization::ensureRolesAndPermissionsExist();

        User::query()->get()->each(function (User $user): void {
            SiteAuthorization::syncSpatieRoleFromLegacyColumns($user);
        });
    }

    public function down(): void
    {
        User::query()->each(function (User $user): void {
            $user->syncRoles([]);
        });

        Role::query()
            ->whereIn('name', array_keys(SiteAuthorization::permissionMap()))
            ->delete();

        Permission::query()
            ->whereIn(
                'name',
                array_map(static fn ($permission) => $permission->value, SiteAuthorization::permissions()),
            )
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
