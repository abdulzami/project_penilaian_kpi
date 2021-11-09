<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaianPerilakusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_perilakus', function (Blueprint $table) {
            $table->id('id_penilaian_perilaku');
            $table->unsignedBigInteger('id_penilaian');
            $table->unsignedBigInteger('id_perilaku');
            $table->integer('nilai_perilaku');
            $table->timestamps();

            $table->foreign('id_penilaian')->references('id_penilaian')->on('penilaians');
            $table->foreign('id_perilaku')->references('id_perilaku')->on('kpi_perilakus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penilaian_perilakus');
    }
}
