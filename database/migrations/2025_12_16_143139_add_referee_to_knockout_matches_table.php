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
            $table->string('referee')->nullable()->after('venue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knockout_matches', function (Blueprint $table) {
            $table->dropColumn('referee');
        });
    }
};
