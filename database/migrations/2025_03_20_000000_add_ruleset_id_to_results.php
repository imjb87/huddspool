<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('ruleset_id')
                ->nullable()
                ->after('section_id')
                ->constrained()
                ->nullOnDelete();
        });

        DB::table('results')
            ->join('fixtures', 'results.fixture_id', '=', 'fixtures.id')
            ->update([
                'results.ruleset_id' => DB::raw('fixtures.ruleset_id'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ruleset_id');
        });
    }
};
