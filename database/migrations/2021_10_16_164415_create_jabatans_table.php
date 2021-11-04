<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJabatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id('id_jabatan');
            $table->string('nama_jabatan');
            $table->unsignedBigInteger('id_bidang');
            $table->unsignedBigInteger('id_penilai')->nullable();
            $table->timestamps();
            
            $table->foreign('id_bidang')->references('id_bidang')->on('bidangs');
            $table->foreign('id_penilai')->references('id_jabatan')->on('jabatans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jabatans');
    }
}
