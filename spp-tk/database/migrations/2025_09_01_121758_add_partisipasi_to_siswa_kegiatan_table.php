<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartisipasiToSiswaKegiatanTable extends Migration
{
    public function up()
    {
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->enum('partisipasi', ['ikut', 'tidak_ikut'])->default('ikut')->after('id_kegiatan');
            $table->text('alasan_tidak_ikut')->nullable()->after('partisipasi');
        });
    }

    public function down()
    {
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->dropColumn(['partisipasi', 'alasan_tidak_ikut']);
        });
    }
}