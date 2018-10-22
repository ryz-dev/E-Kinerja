<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode');
            $table->string('nama_bulan');
            $table->timestamps();
        });
        Schema::table('hari_kerja',function (Blueprint $table){
            $table->foreign('bulan')
                ->references('id')
                ->on('bulan')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hari_kerja',function (Blueprint $table){
            $table->dropForeign('hari_kerja_bulan_foreign');
        });
        Schema::dropIfExists('bulan');
    }
}
