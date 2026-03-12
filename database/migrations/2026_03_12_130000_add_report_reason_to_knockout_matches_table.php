<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knockout_matches', function (Blueprint $table) {
            $table->text('report_reason')->nullable()->after('reported_at');
        });

        DB::table('knockout_matches')
            ->whereNull('report_reason')
            ->whereNotNull('reported_by_id')
            ->update([
                'report_reason' => 'Submitted via frontend.',
            ]);
    }

    public function down(): void
    {
        Schema::table('knockout_matches', function (Blueprint $table) {
            $table->dropColumn('report_reason');
        });
    }
};
