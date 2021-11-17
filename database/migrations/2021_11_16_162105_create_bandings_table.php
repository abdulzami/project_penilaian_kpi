<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBandingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bandings', function (Blueprint $table) {
            $table->id('id_banding');
            $table->unsignedBigInteger('id_penilaian');
            $table->string('alasan',5000);
            $table->string('bukti');
            $table->enum('status_banding',['diterima','ditolak','proses']);
            $table->string('agreement');
            $table->timestamps();

            $table->foreign('id_penilaian')->references('id_penilaian')->on('penilaians');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bandings');
    }
}
