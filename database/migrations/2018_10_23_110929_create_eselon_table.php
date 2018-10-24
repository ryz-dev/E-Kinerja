<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEselonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eselon', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('eselon');
            $table->integer('tunjangan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
        Schema::table('jabatan',function (Blueprint $table){
            $table->foreign('id_eselon')
                ->references('id')
                ->on('jabatan')
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
        Schema::table('jabatan',function (Blueprint $table){
            $table->dropForeign('jabatan_id_eselon_foreign');
        });
        Schema::dropIfExists('eselon');
    }
}
