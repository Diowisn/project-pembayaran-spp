<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaKegiatanTable extends Migration
{
    public function up()
    {
        Schema::create('siswa_kegiatan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_siswa')->unsigned();
            $table->bigInteger('id_kegiatan')->unsigned();
            $table->integer('angsuran_ke');
            $table->integer('jumlah_bayar');
            $table->date('tgl_bayar');
            $table->boolean('is_lunas')->default(false);
            $table->timestamps();

            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('id_kegiatan')->references('id')->on('kegiatan_tahunan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('siswa_kegiatan');
    }
}