<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('tabungan', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('tabungan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('siswa_kegiatan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
