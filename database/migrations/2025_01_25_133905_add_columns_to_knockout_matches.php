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
        Schema::table('knockout_matches', function (Blueprint $table) {
            $table->unsignedInteger('player1_id')->nullable()->after('score2');
            $table->unsignedInteger('player2_id')->nullable()->after('player1_id');
            $table->json('pair1')->nullable()->after('player2_id');
            $table->json('pair2')->nullable()->after('pair1');
            $table->unsignedInteger('team1_id')->nullable()->after('pair2');
            $table->unsignedInteger('team2_id')->nullable()->after('team1_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knockout_matches', function (Blueprint $table) {
            //
        });
    }
};
