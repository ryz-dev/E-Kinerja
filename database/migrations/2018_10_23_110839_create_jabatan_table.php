<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJabatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('jabatan');
            $table->integer('id_eselon')->unsigned()->index();
            $table->integer('id_atasan')->nullable()->unsigned();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_atasan');

            $table->foreign('id_atasan')
                ->references('id')
                ->on('jabatan')
                ->onUpdtae('cascade')
                ->onDelete('restrict');
        });
        Schema::table('pegawai',function (Blueprint $table){
            $table->foreign('id_jabatan')
                ->references('id')
                ->on('jabatan')
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
            $table->dropForeign('pegawai_id_jabatan_foreign');
        });
        Schema::table('jabatan',function (Blueprint $table){
            $table->dropForeign('jabatan_id_atasan_foreign');
        });
        Schema::dropIfExists('jabatan');
    }
}
