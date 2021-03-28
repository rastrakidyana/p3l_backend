<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriBahanKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histori__bahan__keluar', function (Blueprint $table) {
            $table->id('id_histori_keluar');
            $table->biginteger('id_bahan')->unsigned();
            $table->integer('jml_keluar');
            $table->date('tgl_keluar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histori__bahan__keluar');
    }
}
