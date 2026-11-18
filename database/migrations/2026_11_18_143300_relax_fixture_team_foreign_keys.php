<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $foreignKeys = collect(Schema::getForeignKeys('fixtures'))
            ->pluck('name');

        Schema::table('fixtures', function (Blueprint $table) use ($foreignKeys): void {
            if ($foreignKeys->contains('fixtures_home_team_id_foreign')) {
                $table->dropForeign('fixtures_home_team_id_foreign');
            }

            if ($foreignKeys->contains('fixtures_away_team_id_foreign')) {
                $table->dropForeign('fixtures_away_team_id_foreign');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table): void {
            $table->foreign('home_team_id', 'fixtures_home_team_id_foreign')
                ->references('id')
                ->on('teams')
                ->restrictOnDelete();
            $table->foreign('away_team_id', 'fixtures_away_team_id_foreign')
                ->references('id')
                ->on('teams')
                ->restrictOnDelete();
        });
    }
};
