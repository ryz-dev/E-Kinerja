<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgamaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agama', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('agama');
            $table->timestamps();
        });
        Schema::table('pegawai',function (Blueprint $table){
            $table->foreign('id_agama')
                ->references('id')
                ->on('agama')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
            $table->dropForeign('pegawai_id_agama_foreign');
        });
        Schema::dropIfExists('agama');
    }
}
