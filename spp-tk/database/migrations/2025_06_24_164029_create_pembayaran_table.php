<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('pembayaran', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->bigInteger('id_petugas')->unsigned();
    $table->foreign('id_petugas')->references('id')->on('users');
    $table->bigInteger('id_siswa')->unsigned();
    $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
    $table->enum('jenis_pembayaran', ['spp', 'konsumsi', 'infaq_gedung', 'fullday']);
    $table->string('bulan', 10);
    $table->integer('tahun');
    $table->integer('jumlah_bayar');
    $table->boolean('is_lunas')->default(false);
    $table->date('tgl_bayar')->nullable();
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
        Schema::dropIfExists('pembayaran');
    }
}
