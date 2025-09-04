<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKembalianToSiswaKegiatanTable extends Migration
{
    public function up()
    {
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->integer('kembalian')->default(0)->after('jumlah_bayar');
        });
    }

    public function down()
    {
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->dropColumn('kembalian');
        });
    }
}