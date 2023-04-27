<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id');
            $table->integer('home_team_id');
            $table->string('home_team_name');
            $table->integer('home_score');
            $table->integer('away_team_id');
            $table->string('away_team_name');
            $table->integer('away_score');
            $table->boolean('is_confirmed');
            $table->boolean('is_overridden');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
};
