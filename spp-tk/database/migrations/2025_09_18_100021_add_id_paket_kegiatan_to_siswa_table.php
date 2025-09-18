<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdPaketKegiatanToSiswaTable extends Migration
{
    public function up()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('id_paket_kegiatan')
                  ->nullable()
                  ->constrained('kegiatan_tahunan')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['id_paket_kegiatan']);
            $table->dropColumn('id_paket_kegiatan');
        });
    }
}