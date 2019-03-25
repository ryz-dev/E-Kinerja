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
            $table->string('nip_pegawai')->index()->nullable();
            $table->integer('id_skp')->index()->unsigned()->nullable();
            $table->date('periode');
            $table->boolean('status')->default(0);
            $table->dateTime('tanggal_selesai');
            $table->string('nip_update')->index();

            $table->timestamps();
            $table->foreign('nip_pegawai')->references('nip')->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_skp')->references('id')->on('skp')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('nip_update')->references('nip')->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
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
