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
            $table->boolean('is_lunas')->default(false)->after('jumlah_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            //
        });
    }
};
