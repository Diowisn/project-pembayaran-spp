<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_kelas')->unsigned();
            $table->foreign('id_kelas')->references('id')->on('kelas');
            $table->integer('nominal_spp');
            $table->integer('nominal_konsumsi')->nullable();
            $table->integer('nominal_fullday')->nullable();
            $table->integer('tahun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spp');
    }
}
