<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriBahanMasuksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histori__bahan__masuk', function (Blueprint $table) {
            $table->id('id_histori_masuk');
            $table->biginteger('id_bahan')->unsigned();
            $table->integer('jml_masuk');
            $table->date('tgl_masuk');
            $table->double('harga_bahan');
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
        Schema::dropIfExists('histori__bahan__masuk');
    }
}
