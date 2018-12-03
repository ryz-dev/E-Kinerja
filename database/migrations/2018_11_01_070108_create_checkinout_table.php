<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckinoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkinout', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nip');
            $table->timestamp('checktime');
            $table->string('checktype')->nullable();
            $table->string('verifycode')->nullable();
            $table->string('sensorid')->nullable();
            $table->string('workcode')->nullable();
            $table->string('sn')->nullable();
            $table->string('userextmft')->nullable();
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
        Schema::dropIfExists('checkinout');
    }
}
