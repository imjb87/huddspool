<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->integer('section_id');
        });

        // copy section_id from fixtures to results
        DB::table('results')
            ->join('fixtures', 'results.fixture_id', '=', 'fixtures.id')
            ->update(['results.section_id' => DB::raw('fixtures.section_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            //
        });
    }
};
