<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKinerjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kinerja', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userid')->index();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->enum('jenis_kinerja',['hadir','perjalanan_dinas','cuti','izin','sakit']);
            $table->text('rincian_kinerja');
            $table->boolean('approve')->nullable();
            $table->text('keterangan_approve')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kinerja');
    }
}
