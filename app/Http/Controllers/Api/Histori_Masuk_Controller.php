<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Histori_Bahan_Masuk;

class Histori_Masuk_Controller extends Controller
{
    public function index(){
        $masuks = Histori_Bahan_Masuk::all();

        if(count($masuks) > 0){
            return response([
                'message' => 'Tampil Semua Histori Bahan Masuk Berhasil',
                'data' => $masuks
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $masuk = Histori_Bahan_Masuk::find($id);

        if(!is_null($masuk)){
            return response([
                'message' => 'Tampil Histori Bahan Masuk Berhasil',
                'data' => $masuk
            ],200);
        }

        return response([
            'message' => 'Histori Bahan Masuk Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [            
            'id_bahan' => 'required',
            'jml_masuk' => 'required|numeric',
            'tgl_masuk' => 'required',
            'harga_bahan' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        
                

        $masuk = Histori_Bahan_Masuk::create($store_data);
        return response([
            'message' => 'Tambah Histori Bahan Masuk Berhasil',
            'data' => $masuk,
        ],200);
    }

    public function update(Request $request, $id){
        $masuk = Histori_Bahan_Masuk::find($id);

        if(is_null($masuk)){
            return response([
                'message' => 'Histori Bahan Masuk Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'id_bahan' => 'required',
            'jml_masuk' => 'required|numeric',
            'tgl_masuk' => 'required',
            'harga_bahan' => 'required|numeric',
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $masuk->id_bahan = $update_data['id_bahan'];
        $masuk->jml_masuk = $update_data['jml_masuk'];
        $masuk->tgl_masuk = $update_data['tgl_masuk'];
        $masuk->harga_bahan = $update_data['harga_bahan'];
 
        if($masuk->save()){
             return response([
                 'message' => 'Ubah Histori Bahan Masuk Berhasil',
                 'data' => $masuk,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Histori Bahan Masuk Gagal',
            'data' => null,
            ],400);
    }

    // public function destroy($id){
    //     $masuk = Histori_Bahan_Masuk::find($id);
 
    //     if(is_null($masuk)){
    //         return response([
    //             'message' => 'Histori Bahan Masuk Tidak Ditemukan',
    //             'data' => null
    //         ],404);
    //     }
        
    //     $bahan->status_hapus = 1;

    //     if($bahan->save()){
    //         return response([
    //             'message' => 'Hapus Bahan Berhasil',
    //             'data' => $bahan,
    //             ],200);
    //     }
 
    //     return response([
    //         'message' => 'Hapus Bahan Gagal',
    //         'data' => null,
    //     ],400);
    // }
}
