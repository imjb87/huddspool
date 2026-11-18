<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Support\SiteAuthorization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                SiteAuthorization::syncSpatieRoleFromLegacyColumns($user);
            });

        $invalidTeamUserIds = DB::table('users as users')
            ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
            ->whereNotNull('users.team_id')
            ->whereNull('teams.id')
            ->pluck('users.id');

        if ($invalidTeamUserIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('id', $invalidTeamUserIds)
                ->update(['team_id' => null]);
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('team_id', 'users_team_id_foreign')
                ->references('id')
                ->on('teams')
                ->nullOnDelete();
            $table->dropColumn(['role', 'is_admin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign('users_team_id_foreign');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default(UserRole::Player->value);
            $table->boolean('is_admin')->default(false);
        });

        $adminRoleId = DB::table('roles')
            ->where('name', 'admin')
            ->where('guard_name', 'web')
            ->value('id');
        $teamAdminRoleId = DB::table('roles')
            ->where('name', 'team-admin')
            ->where('guard_name', 'web')
            ->value('id');

        if ($adminRoleId) {
            DB::table('users')
                ->whereIn('id', DB::table('model_has_roles')
                    ->where('model_type', User::class)
                    ->where('role_id', $adminRoleId)
                    ->pluck('model_id'))
                ->update([
                    'role' => UserRole::Player->value,
                    'is_admin' => true,
                ]);
        }

        if ($teamAdminRoleId) {
            DB::table('users')
                ->whereIn('id', DB::table('model_has_roles')
                    ->where('model_type', User::class)
                    ->where('role_id', $teamAdminRoleId)
                    ->pluck('model_id'))
                ->where('is_admin', false)
                ->update(['role' => UserRole::TeamAdmin->value]);
        }
    }
};
