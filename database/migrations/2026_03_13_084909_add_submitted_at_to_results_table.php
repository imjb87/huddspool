<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');
        });

        DB::table('results')
            ->where('is_confirmed', true)
            ->whereNull('submitted_at')
            ->update([
                'submitted_at' => DB::raw('created_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('submitted_at');
        });
    }
};
