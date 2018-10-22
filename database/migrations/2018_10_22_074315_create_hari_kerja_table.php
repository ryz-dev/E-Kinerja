<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHariKerjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hari_kerja', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal')->unique();
            $table->integer('tahun');
            $table->integer('bulan')->unsigned()->index();
            $table->integer('hari')->unsigned()->index();
            $table->integer('id_status_hari')->unsigned()->index();
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
        Schema::dropIfExists('hari_kerja');
    }
}
