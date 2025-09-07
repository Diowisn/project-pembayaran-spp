<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdPembayaranInfaqToTabunganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pembayaran_infaq')->nullable()->after('id_siswa');

            $table->foreign('id_pembayaran_infaq')
                  ->references('id')
                  ->on('angsuran_infaq')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabungan', function (Blueprint $table) {
            $table->dropForeign(['id_pembayaran_infaq']);
            $table->dropColumn('id_pembayaran_infaq');
        });
    }
}