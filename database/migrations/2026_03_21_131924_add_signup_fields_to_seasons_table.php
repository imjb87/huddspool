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
        Schema::table('seasons', function (Blueprint $table) {
            $table->timestamp('signup_opens_at')->nullable()->after('dates');
            $table->timestamp('signup_closes_at')->nullable()->after('signup_opens_at');
            $table->decimal('team_entry_fee', 8, 2)->default(0)->after('signup_closes_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn([
                'signup_opens_at',
                'signup_closes_at',
                'team_entry_fee',
            ]);
        });
    }
};
