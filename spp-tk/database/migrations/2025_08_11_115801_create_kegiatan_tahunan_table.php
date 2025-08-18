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
        Schema::create('uang_tahunan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('id_pembayaran')->nullable()->constrained('pembayaran')->onDelete('set null');
            $table->foreignId('id_petugas')->constrained('users');
            $table->integer('tahun_ajaran');
            $table->integer('debit')->default(0);
            $table->integer('kredit')->default(0);
            $table->integer('saldo')->default(0);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_tahunan');
    }
};
