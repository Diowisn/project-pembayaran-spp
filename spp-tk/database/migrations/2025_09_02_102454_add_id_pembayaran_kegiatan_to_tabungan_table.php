<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pembayaran_kegiatan')->nullable()->after('id_pembayaran');
            $table->foreign('id_pembayaran_kegiatan')->references('id')->on('siswa_kegiatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabungan', function (Blueprint $table) {
            //
        });
    }
};
