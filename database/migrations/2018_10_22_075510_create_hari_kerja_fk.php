<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHariKerjaFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hari_kerja',function (Blueprint $table){
            $table->foreign('hari')
                ->references('id')
                ->on('hari')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_status_hari')
                ->references('id')
                ->on('status_hari')
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
        Schema::table('hari_kerja', function (Blueprint $table) {
            $table->dropForeign('hari_kerja_hari_foreign');
            $table->dropForeign('hari_kerja_id_status_hari_foreign');
        });
    }
}
