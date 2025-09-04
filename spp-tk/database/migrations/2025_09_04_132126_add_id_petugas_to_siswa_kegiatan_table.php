<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdPetugasToSiswaKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return \Illuminate\Http\Response
     */
    public function up()
    {
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->foreignId('id_petugas')->nullable()->after('kembalian')
                  ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return \Illuminate\Http\Response
     */
    public function down()
    {
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['id_petugas']);
            $table->dropColumn('id_petugas');
        });
    }
}