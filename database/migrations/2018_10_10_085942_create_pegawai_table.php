<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai',function(Blueprint $table){
            $table->uuid('uuid');
            $table->string('nip');
            $table->string('userid')->index()->nullable();
//            $table->integer('id_fp')->nullable()->unsigned()->index();
            $table->string('foto')->nullable();
            $table->string('nama');
            $table->date('tanggal_lahir');
//            $table->string('unit_kerja')->nullable()->index();
//            $table->boolean('status_upload')->default(false);
            $table->integer('id_agama')->unsigned()->index();
            $table->integer('id_jabatan')->nullable()->unsigned()->index();
            $table->enum('jns_kel',['laki-laki','perempuan']);
            $table->string('tempat_lahir');
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
        Schema::dropIfExists('pegawai');
    }
}
