<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkpKinerjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skp_kinerja', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->integer('id_kinerja')->unsigned()->index();
            $table->integer('id_skp_pegawai')->unsigned()->index();

            $table->foreign('id_kinerja')
                ->on('kinerja')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('id_skp_pegawai')
                ->on('skp_pegawai')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('skp_kinerja');
    }
}
