<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kartu extends Model
{
    protected $table = 'kartu';

    protected $fillable = [
        'no_kartu', 'nama_kartu', 'tgl_kadaluarsa',
        'tipe_kartu'
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

    public function transaksis(){
        return $this->belongsToMany('App\Transaksi');
    }
}
