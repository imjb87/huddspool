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
        Schema::table('knockouts', function (Blueprint $table) {
            $table->decimal('entry_fee', 8, 2)->default(0)->after('best_of');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knockouts', function (Blueprint $table) {
            $table->dropColumn('entry_fee');
        });
    }
};
