<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('season_team_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('season_team_entries', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('team_name');
            }

            if (! Schema::hasColumn('season_team_entries', 'contact_telephone')) {
                $table->string('contact_telephone')->nullable()->after('contact_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('season_team_entries', function (Blueprint $table) {
            if (Schema::hasColumn('season_team_entries', 'contact_telephone')) {
                $table->dropColumn('contact_telephone');
            }

            if (Schema::hasColumn('season_team_entries', 'contact_name')) {
                $table->dropColumn('contact_name');
            }
        });
    }
};
