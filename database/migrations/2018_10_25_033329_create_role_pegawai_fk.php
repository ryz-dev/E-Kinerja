<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolePegawaiFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_pegawai', function(Blueprint $table) {
            $table->foreign('nip_pegawai')
                ->references('nip')
                ->on('pegawai')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign('id_role')
                ->references('id')
                ->on('role')
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
        Schema::table('role_pegawai', function(Blueprint $table){
            $table->dropForeign('role_pegawai_nip_pegawai_foreign');
            $table->dropForeign('role_pegawai_id_role_foreign');
        });
    }
}
