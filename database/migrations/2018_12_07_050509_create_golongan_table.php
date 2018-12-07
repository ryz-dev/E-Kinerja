<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGolonganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golongan', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('golongan');
            $table->integer('tunjangan');
            $table->string('kriteria')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
        Schema::table('jabatan',function (Blueprint $table){
            $table->foreign('id_golongan')
                ->references('id')
                ->on('golongan')
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
        Schema::table('jabatan',function (Blueprint $table){
            $table->dropForeign('jabatan_id_golongan_foreign');
        });
        Schema::dropIfExists('golongan');
    }
}
