<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('season_team_entries', function (Blueprint $table) {
            if (Schema::hasColumn('season_team_entries', 'ruleset_id')) {
                return;
            }

            $table->foreignId('ruleset_id')
                ->nullable()
                ->after('existing_team_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('season_team_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('season_team_entries', 'ruleset_id')) {
                return;
            }

            $table->dropConstrainedForeignId('ruleset_id');
        });
    }
};
