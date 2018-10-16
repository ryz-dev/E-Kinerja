<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPegawai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pegawai',function(Blueprint $table){
            $table->string('nip');
            $table->integer('id_fp')->unsigned()->index();
            $table->string('nama');
            $table->date('tanggal_lahir');
            $table->string('unit_kerja')->index();
            $table->boolean('status_upload');
            $table->integer('agama')->unsigned()->index();
            $table->string('kode_jabatan')->index();
            $table->enum('jns_kel',['Laki - Laki','Perempuan']);
            $table->string('templat_lahir');
            $table->softDeletes();
            $table->timestamps();

            $table->primary('nip');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pegawai');
    }
}
