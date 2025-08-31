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
        Schema::table('siswa', function (Blueprint $table) {
            $table->bigInteger('id_inklusi')->unsigned()->nullable()->after('inklusi');
            $table->foreign('id_inklusi')->references('id')->on('inklusi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // $table->dropColumn('id_inklusi');
            $table->dropForeign(['id_inklusi']);
            $table->dropColumn(['inklusi', 'id_inklusi']);
        });
    }
};