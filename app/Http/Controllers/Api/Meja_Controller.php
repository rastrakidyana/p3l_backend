<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Meja;

class Meja_Controller extends Controller
{
    public function index(){
        $mejas = Meja::all();

        if(count($mejas) > 0){
            return response([
                'message' => 'Tampil Semua Meja Berhasil',
                'data' => $mejas
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $meja = Meja::find($id);

        if(!is_null($meja)){
            return response([
                'message' => 'Tampil Meja Berhasil',
                'data' => $meja
            ],200);
        }

        return response([
            'message' => 'Meja Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'no_meja' => 'required|numeric|',                                      
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        

        $store_data['status_meja'] = 'Tersedia';
        $store_data['status_hapus'] = 0;

        $meja = Meja::create($store_data);
        return response([
            'message' => 'Tambah Meja Berhasil',
            'data' => $meja,
        ],200);
    }

    public function update(Request $request, $id){
        $meja = Meja::find($id);

        if(is_null($meja)){
            return response([
                'message' => 'Meja Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'no_meja' => 'required|numeric|',
            'status_meja' => 'required',    
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $meja->no_meja = $update_data['no_meja'];
        $meja->status_meja = $update_data['status_meja'];
 
        if($meja->save()){
             return response([
                 'message' => 'Ubah Meja Berhasil',
                 'data' => $meja,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Meja Gagal',
            'data' => null,
            ],400);
    }

    public function destroy($id){
        $meja = Meja::find($id);
 
        if(is_null($meja)){
            return response([
                'message' => 'Meja Tidak Ditemukan',
                'data' => null
            ],404);
        }
        
        $meja->status_meja = 'Tidak Tersedia';
        $meja->status_hapus = 1;

        if($meja->save()){
            return response([
                'message' => 'Hapus Meja Berhasil',
                'data' => $meja,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Meja Gagal',
            'data' => null,
        ],400);
    }
}
