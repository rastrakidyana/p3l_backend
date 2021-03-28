<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->integer('id_jabatan');           
            $table->string('nama_karyawan', 50)->unique();
            $table->string('jk_karyawan', 20);
            $table->string('telp_karyawan', 20)->unique();
            $table->string('email_karyawan', 30)->unique();
            $table->date('tgl_gabung_karyawan');
            $table->string('password_karyawan', 20);
            $table->string('status_karyawan', 20);
            $table->timestamp('email_verified_at')->nullable();            
            $table->rememberToken();
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
        Schema::dropIfExists('karyawan');
    }
}
