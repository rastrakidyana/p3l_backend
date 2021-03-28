<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Histori_Bahan_Masuk extends Model
{
    protected $table = 'histori_bahan_masuk';

    protected $fillable = [
        'id_bahan', 'jml_masuk', 'tgl_masuk',
        'harga_bahan'
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
        return $this->belongsTo('App\Bahan');
    }
}
