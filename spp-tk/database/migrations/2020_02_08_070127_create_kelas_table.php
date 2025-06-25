 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('kelas', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('nama_kelas', 20); // TPA, KBT, TK A, TK B
    $table->boolean('has_konsumsi')->default(false);
    $table->boolean('has_fullday')->default(false);
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
        Schema::dropIfExists('kelas');
    }
}
