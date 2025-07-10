<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPasswordToSiswaTable extends Migration
{
    public function up()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('password')
                  ->after('nama')
                  ->default('$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // Hash dari "123456"
        });
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
}