<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkpPegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skp_pegawai', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->string('nip_pegawai')->unsigned()->nullable();
            $table->integer('id_skp')->unsigned()->nullable();
            $table->date('periode');
            $table->boolean('status')->default(0);
            $table->dateTime('tanggal_selesai');
            $table->string('nip_update')->unsigned();

            $table->foreign('nip_pegawai')->reference('nip')->on('pegawai');
            $table->foreign('id_skp')->reference('id')->on('skp');
            $table->foreign('nip_update')->reference('nip')->on('pegawai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skp_pegawai');
        // Schema::table('skp_pegawai', function (Blueprint $table) {
        //     $table->dropForeign('skp_pegawai_nip_pegawai_foreign');
        //     $table->dropForeign('skp_pegawai_id_skp_foreign');
        //     $table->dropForeign('skp_pegawai_nip_update_foreign');
        // });
    }
}
