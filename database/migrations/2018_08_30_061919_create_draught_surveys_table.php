<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDraughtSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draught_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('barging_id')->unsigned();
            $table->integer('barging_material_id')->unsigned();
            $table->integer('volume');
            $table->integer('user_id')->unsigned();
            $table->string('pic');
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
        Schema::dropIfExists('draught_surveys');
    }
}
