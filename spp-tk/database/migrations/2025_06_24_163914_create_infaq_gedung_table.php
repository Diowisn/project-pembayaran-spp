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
Schema::create('infaq_gedung', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('paket', 1); // A, B, C
    $table->integer('nominal');
    $table->integer('jumlah_angsuran')->default(12); // default 1 tahun
    $table->integer('nominal_per_angsuran');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infaq_gedung');
    }
};
