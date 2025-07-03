<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('siswa', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->char('nisn', 12)->unique();
    // $table->char('nis', 8)->unique();
    $table->string('nama', 35);
    $table->bigInteger('id_kelas')->unsigned();
    $table->foreign('id_kelas')->references('id')->on('kelas');
    $table->text('alamat');
    $table->string('nomor_telp');
    $table->bigInteger('id_infaq_gedung')->unsigned()->nullable();
    $table->foreign('id_infaq_gedung')->references('id')->on('infaq_gedung');
    $table->bigInteger('id_spp')->unsigned();
    $table->foreign('id_spp')->references('id')->on('spp');
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
        Schema::dropIfExists('siswa');
    }
}
