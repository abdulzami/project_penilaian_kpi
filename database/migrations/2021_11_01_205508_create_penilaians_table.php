<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id('id_penilaian');
            $table->unsignedBigInteger('id_pegawai');
            $table->unsignedBigInteger('id_jadwal');
            $table->string('status_penilaian');
            $table->string('catatan_penting');
            $table->integer('pengurangan');
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_user')->on('users');
            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penilaians');
    }
}
