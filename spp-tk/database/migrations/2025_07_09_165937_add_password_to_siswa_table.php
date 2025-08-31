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
                  ->default('$2y$12$nykLZDBC2.QWbth5Rp9BcOiZcZqeH9w5/LlIGwckycfc6.UUW1Ou6');
        });
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
}