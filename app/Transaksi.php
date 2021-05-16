<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'id_kartu', 'id_karyawan', 'id_reservasi',
        'no_transaksi', 'total_transaksi', 'metode_pembayaran',
        'kode_edc', 'status_transaksi'
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

    public function kartu(){
        return $this->hasOne('App\Kartu');
    }

    public function reservasi(){
        return $this->belongsTo('App\Reservasi');
    }

    public function pesanans(){
        return $this->hasMany('App\Pesanan');
    }
}
