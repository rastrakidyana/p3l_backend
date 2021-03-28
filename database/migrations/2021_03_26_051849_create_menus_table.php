<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->biginteger('id_bahan')->unsigned();
            $table->string('nama_menu', 30);
            $table->string('deskripsi_menu');
            $table->string('unit_menu', 20);
            $table->double('harga_menu');
            $table->string('tipe_menu', 20);
            $table->integer('stok_menu');
            $table->longtext('gambar_menu');
            $table->integer('serving_size');
            $table->integer('status_hapus');
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
        Schema::dropIfExists('menu');
    }
}
