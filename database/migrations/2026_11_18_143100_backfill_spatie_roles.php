<?php

use App\Models\User;
use App\Support\SiteAuthorization;
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
