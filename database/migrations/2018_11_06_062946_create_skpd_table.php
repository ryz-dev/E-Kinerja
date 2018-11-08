<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkpdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skpd', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_skpd');
            $table->text('keterangan');
            $table->timestamps();
        });
        Schema::table('pegawai',function (Blueprint $table){
            $table->foreign('id_skpd')->references('id')
                ->on('skpd')
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
        Schema::table('pegawai',function (Blueprint $table){
            $table->dropForeign('pegawai_id_skpd_foreign');
        });
        Schema::dropIfExists('skpd');
    }
}
