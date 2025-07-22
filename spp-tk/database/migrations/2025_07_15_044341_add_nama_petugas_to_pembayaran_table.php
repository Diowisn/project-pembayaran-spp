<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id_petugas');
        });

        // Update data yang sudah ada
        DB::statement("
            UPDATE pembayaran p
            JOIN users u ON p.id_petugas = u.id
            SET p.name = u.name
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
