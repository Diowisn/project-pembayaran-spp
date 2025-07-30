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
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->dropForeign(['id_siswa']);

            $table->foreign('id_siswa')
                  ->references('id')
                  ->on('siswa')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->dropForeign(['id_siswa']);

            $table->foreign('id_siswa')
                  ->references('id')
                  ->on('siswa');
        });
    }
};
