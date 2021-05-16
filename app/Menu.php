<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'id_bahan', 'nama_menu', 'deskripsi_menu',
        'unit_menu', 'harga_menu', 'tipe_menu',
        'stok_menu', 'gambar_menu', 'serving_size',
        'status_hapus'
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

    public function bahan(){
        return $this->hasOne('App\Bahan', 'id_bahan');
    }

    public function pesanans(){
        return $this->belongsToMany('App\Pesanan');
    }
}
