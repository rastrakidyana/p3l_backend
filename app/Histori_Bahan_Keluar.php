<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Histori_Bahan_Keluar extends Model
{
    protected $table = 'histori__bahan__keluar';

    protected $fillable = [
        'id_bahan', 'jml_keluar', 'tgl_keluar', 'status_keluar'
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
