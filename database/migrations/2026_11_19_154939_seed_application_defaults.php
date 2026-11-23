<?php

use App\Domain\Authorization\SiteAuthorization;
use Database\Seeders\NotificationSettingsSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SiteAuthorization::ensureRolesAndPermissionsExist();
        app(NotificationSettingsSeeder::class)->run();
    }

    /**
     * Preserve defaults that may be referenced by users or have customized settings.
     */
    public function down(): void {}
};
