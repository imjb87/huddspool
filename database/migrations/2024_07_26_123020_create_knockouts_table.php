<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Knockout;

class CreateKnockoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knockouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', [Knockout::TYPE_SINGLES, Knockout::TYPE_DOUBLES, Knockout::TYPE_TEAM]);
            $table->foreignId('season_id');
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knockouts');
    }
}
