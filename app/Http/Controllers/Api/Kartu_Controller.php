<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Transaksi;
use App\Kartu;

class Kartu_Controller extends Controller
{
    public function index(){
        $kartus = Kartu::orderBy('created_at', 'DESC')->get();

        if(count($kartus) > 0){
            return response([
                'message' => 'Tampil Semua Kartu Berhasil',
                'data' => $kartus
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function indexTipe($tipe){
        $kartus = Kartu::orderBy('created_at', 'DESC')->where('tipe_kartu', '=', $tipe)->get();

        if(count($kartus) > 0){
            return response([
                'message' => 'Tampil Semua Kartu Berhasil',
                'data' => $kartus
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $kartu = Kartu::find($id);

        if(!is_null($kartu)){
            return response([
                'message' => 'Tampil Kartu Berhasil',
                'data' => $kartu
            ],200);
        }

        return response([
            'message' => 'Kartu Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'no_kartu' => 'required',
            'tgl_kadaluarsa' => 'required',            
            'tipe_kartu' => 'required',            
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);       
                        

        $kartu = Kartu::create($store_data); 
        
        return response([
            'message' => 'Tambah Kartu Berhasil',
            'data' => $kartu,
        ],200);
    }
    
    public function cekKartu($no){
        $kartu = Kartu::where('no_kartu', '=', $no)->first();

        if(!is_null($kartu)){
            return response([
                'data' => 'Ada',
            ],200);
        }

        return response([
            'data' => 'Kosong',
        ],200);
    }

}
