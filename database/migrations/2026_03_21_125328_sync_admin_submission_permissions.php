<?php

use App\Support\SiteAuthorization;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SiteAuthorization::ensureRolesAndPermissionsExist();
    }

    public function down(): void
    {
        SiteAuthorization::ensureRolesAndPermissionsExist();
    }
};
