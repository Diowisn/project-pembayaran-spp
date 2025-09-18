<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyKegiatanTahunanTableNullable extends Migration
{
    public function up()
    {
        Schema::table('kegiatan_tahunan', function (Blueprint $table) {
            $table->string('nama_kegiatan')->nullable()->change();
            $table->integer('nominal')->nullable()->change();
            $table->text('keterangan')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('kegiatan_tahunan', function (Blueprint $table) {
            $table->string('nama_kegiatan')->nullable(false)->change();
            $table->integer('nominal')->nullable(false)->change();
            $table->text('keterangan')->nullable(false)->change();
        });
    }
}