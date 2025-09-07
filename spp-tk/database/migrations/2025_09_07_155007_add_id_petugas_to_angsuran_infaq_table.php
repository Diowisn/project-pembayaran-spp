<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdPetugasToAngsuranInfaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->unsignedBigInteger('id_petugas')->nullable()->after('id_siswa');
            
            // Foreign key constraint
            $table->foreign('id_petugas')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('angsuran_infaq', function (Blueprint $table) {
            $table->dropForeign(['id_petugas']);
            $table->dropColumn('id_petugas');
        });
    }
}