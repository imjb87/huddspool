<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frames', function (Blueprint $table): void {
            $table->unsignedBigInteger('home_player_id')->nullable()->change();
            $table->unsignedBigInteger('away_player_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        $hasFramesWithoutPlayers = DB::table('frames')
            ->whereNull('home_player_id')
            ->orWhereNull('away_player_id')
            ->exists();

        if ($hasFramesWithoutPlayers) {
            throw new RuntimeException('Cannot make frame player IDs required while frames contain null player IDs.');
        }

        Schema::table('frames', function (Blueprint $table): void {
            $table->unsignedBigInteger('home_player_id')->nullable(false)->change();
            $table->unsignedBigInteger('away_player_id')->nullable(false)->change();
        });
    }
};
