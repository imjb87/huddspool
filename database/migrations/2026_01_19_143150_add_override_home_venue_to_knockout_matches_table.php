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
            $table->boolean('override_home_venue')->default(false)->after('venue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knockout_matches', function (Blueprint $table) {
            $table->dropColumn('override_home_venue');
        });
    }
};
