<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rulesets', function (Blueprint $table) {
            // add slug column
            $table->string('slug')->after('name');
        });

        $rulesets = DB::table('rulesets')
            ->select('id', 'name')
            ->get();

        foreach ($rulesets as $ruleset) {
            DB::table('rulesets')
                ->where('id', $ruleset->id)
                ->update(['slug' => Str::slug($ruleset->name)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rulesets', function (Blueprint $table) {
            //
        });
    }
};
