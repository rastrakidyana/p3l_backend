<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'id_menu', 'id_transaksi', 'jml_pesanan',
        'total_pesanan', 'status_pesanan'
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

    public function menu(){
        return $this->hasOne('App\Menu');
    }

    public function transaksi(){
        return $this->belongsTo('App\Transaksi');
    }
}
