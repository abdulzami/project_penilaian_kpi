<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriPenilaianPerformancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histori_penilaian_performances', function (Blueprint $table) {
            $table->id('id_histori_penilaian_performance');
            $table->unsignedBigInteger('id_penilaian');
            $table->unsignedBigInteger('id_performance');
            $table->float('realisasi');
            $table->timestamps();

            $table->foreign('id_penilaian')->references('id_penilaian')->on('penilaians');
            $table->foreign('id_performance')->references('id_performance')->on('kpi_performances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histori_penilaian_performances');
    }
}
