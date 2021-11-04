<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('npk')->unique();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status_user',['aktif','nonaktif']);
            $table->enum('level',['admin','pegawai']);
            $table->unsignedBigInteger('id_jabatan')->nullable();
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
        Schema::dropIfExists('users');
    }
}
