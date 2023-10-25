<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Ruleset;
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

        $rulesets = Ruleset::all();

        foreach ($rulesets as $ruleset) {
            $ruleset->slug = Str::slug($ruleset->name);
            $ruleset->save();
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
