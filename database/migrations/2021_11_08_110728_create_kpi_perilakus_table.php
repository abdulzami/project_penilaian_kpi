<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiPerilakusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_perilakus', function (Blueprint $table) {
            $table->id('id_perilaku');
            $table->string('nama_kpi');
            $table->string('ekselen',1000);
            $table->string('baik',1000);
            $table->string('cukup',1000);
            $table->string('kurang',1000);
            $table->string('kurang_sekali',1000);
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
        Schema::dropIfExists('kpi_perilakus');
    }
}
