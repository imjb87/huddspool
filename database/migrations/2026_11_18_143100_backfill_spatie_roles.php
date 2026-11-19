<?php

use App\Domain\Authorization\SiteAuthorization;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        SiteAuthorization::ensureRolesAndPermissionsExist();

        User::withTrashed()
            ->cursor()
            ->each(function (User $user): void {
                SiteAuthorization::assignRole(
                    $user,
                    SiteAuthorization::inferRoleNameFromLegacy(
                        $user->role !== null ? (string) $user->role : null,
                        (bool) $user->is_admin,
                    ),
                );
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
