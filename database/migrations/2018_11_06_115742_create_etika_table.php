<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtikaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etika', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nip')->index();
            $table->date('tanggal');
            $table->integer('persentase');
            $table->integer('mengikuti_upacara');
            $table->integer('perilaku_kerja');
            $table->integer('kegiatan_kebersamaan');
            $table->text('keterangan');
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
        Schema::dropIfExists('etika');
    }
}
