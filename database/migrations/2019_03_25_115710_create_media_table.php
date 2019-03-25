<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->integer('id_kinerja')->unsigned()->index();
            $table->string('media');
            $table->timestamps();

            $table->foreign('id_kinerja')
                ->on('kinerja')
                ->references('id')
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
        Schema::table('media',function(Blueprint $table){
            $table->dropForeign('media_id_kinerja_foreign');
        });
        Schema::dropIfExists('media');
    }
}
