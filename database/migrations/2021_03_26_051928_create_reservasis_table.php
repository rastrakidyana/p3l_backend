<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id('id_reservasi');
            $table->biginteger('id_customer')->unsigned();
            $table->biginteger('id_meja')->unsigned();
            $table->biginteger('id_karyawan')->unsigned();
            $table->biginteger('id_transaksi')->unsigned()->nullable();
            $table->date('tgl_reservasi');
            $table->string('jadwal_kunjungan', 20);
            $table->string('kode_qr');
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
        Schema::dropIfExists('reservasi');
    }
}
