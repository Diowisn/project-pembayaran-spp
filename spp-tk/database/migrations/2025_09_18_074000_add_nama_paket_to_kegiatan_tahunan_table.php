<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNamaPaketToKegiatanTahunanTable extends Migration
{
    public function up()
    {
        Schema::table('kegiatan_tahunan', function (Blueprint $table) {
            $table->string('nama_paket')->after('id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('kegiatan_tahunan', function (Blueprint $table) {
            $table->dropColumn('nama_paket');
        });
    }
}