<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->biginteger('id_kartu')->unsigned()->nullable();
            $table->biginteger('id_karyawan')->unsigned();
            $table->biginteger('id_reservasi')->unsigned();
            $table->string('no_transaksi', 20);
            $table->double('total_transaksi');
            $table->string('metode_pembayaran', 20)->nullable();
            $table->string('kode_edc', 20);
            $table->string('status_transaksi', 20);
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
        Schema::dropIfExists('transaksi');
    }
}
