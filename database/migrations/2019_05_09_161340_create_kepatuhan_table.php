<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKepatuhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kepatuhan', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('nip')->index();
            $table->date('periode');
            $table->boolean('lkpn')->default(false);
            $table->boolean('bmd')->default(false);
            $table->boolean('tptgr')->default(false);
            $table->timestamps();

            $table->foreign('nip')->references('nip')->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kepatuhan');
    }
}
