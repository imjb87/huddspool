<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('season_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('season_entries', 'existing_venue_id')) {
                $table->foreignId('existing_venue_id')
                    ->nullable()
                    ->after('contact_telephone')
                    ->constrained('venues')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('season_entries', 'venue_name')) {
                $table->string('venue_name')->nullable()->after('existing_venue_id');
            }

            if (! Schema::hasColumn('season_entries', 'venue_address')) {
                $table->text('venue_address')->nullable()->after('venue_name');
            }

            if (! Schema::hasColumn('season_entries', 'venue_telephone')) {
                $table->string('venue_telephone')->nullable()->after('venue_address');
            }
        });

        Schema::table('season_team_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('season_team_entries', 'second_ruleset_id')) {
                $table->foreignId('second_ruleset_id')
                    ->nullable()
                    ->after('ruleset_id')
                    ->constrained('rulesets')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('season_entries', function (Blueprint $table) {
            if (Schema::hasColumn('season_entries', 'existing_venue_id')) {
                $table->dropConstrainedForeignId('existing_venue_id');
            }

            if (Schema::hasColumn('season_entries', 'venue_telephone')) {
                $table->dropColumn('venue_telephone');
            }

            if (Schema::hasColumn('season_entries', 'venue_address')) {
                $table->dropColumn('venue_address');
            }

            if (Schema::hasColumn('season_entries', 'venue_name')) {
                $table->dropColumn('venue_name');
            }
        });

        Schema::table('season_team_entries', function (Blueprint $table) {
            if (Schema::hasColumn('season_team_entries', 'second_ruleset_id')) {
                $table->dropConstrainedForeignId('second_ruleset_id');
            }
        });
    }
};
