<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKembalianToAngsuranInfaqTable extends Migration
{
    public function up()
    {
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->decimal('kembalian', 15, 2)->default(0)->after('jumlah_bayar');
        });
    }

    public function down()
    {
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->dropColumn('kembalian');
        });
    }
}