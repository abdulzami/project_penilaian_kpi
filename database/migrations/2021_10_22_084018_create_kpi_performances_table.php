<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiPerformancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_performances', function (Blueprint $table) {
            $table->id('id_performance');
            $table->enum('kategori',['kualitas','waktu','kuantitas','biaya']);
            $table->enum('tipe_performance',['maximum','minimize']);
            $table->unsignedBigInteger('id_jabatan');
            $table->string('indikator_kpi');
            $table->string('definisi');
            $table->string('satuan');
            $table->float('target');
            $table->integer('bobot');
            $table->timestamps();

            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpi_performances');
    }
}
