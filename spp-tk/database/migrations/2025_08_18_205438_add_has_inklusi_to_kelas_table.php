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
        Schema::table('kelas', function (Blueprint $table) {
            $table->boolean('has_inklusi')->default(false)->after('has_fullday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('has_inklusi');
        });
    }
};
