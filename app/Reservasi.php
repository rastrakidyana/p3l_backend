<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = [
        'id_customer', 'id_meja', 'id_karyawan',
        'id_transaksi', 'tgl_reservasi', 'jadwal_kunjungan',
        'kode_qr', 'status_hapus'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }

    public function karyawan(){
        return $this->hasOne('App\Karyawan');
    }

    public function customer(){
        return $this->hasOne('App\Customer');
    }

    public function meja(){
        return $this->hasOne('App\Meja');
    }
}
