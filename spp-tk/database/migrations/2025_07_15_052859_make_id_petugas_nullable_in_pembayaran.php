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
        $table->unsignedBigInteger('id_petugas')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::table('pembayaran', function (Blueprint $table) {
        $table->unsignedBigInteger('id_petugas')->nullable(false)->change();
    });
}
};
