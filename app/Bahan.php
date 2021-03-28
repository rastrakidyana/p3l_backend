<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bahan extends Model
{
    protected $table = 'bahan';

    protected $fillable = [
        'id_menu', 'nama_bahan', 'stok_bahan',
        'unit_bahan', 'status_hapus'
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
        return $this->belongsTo('App\Menu');
    }

    public function histori_masuks(){
        return $this->hasMany('App\Histori_Bahan_Masuk');
    }
}
