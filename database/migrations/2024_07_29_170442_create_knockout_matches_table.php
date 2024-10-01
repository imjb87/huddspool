<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnockoutMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knockout_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id');
            $table->foreignId('venue_id')->nullable();
            $table->integer('score1')->nullable();
            $table->integer('score2')->nullable();
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
        Schema::dropIfExists('knockout_matches');
    }
}
