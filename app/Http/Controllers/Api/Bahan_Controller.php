<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Bahan;

class Bahan_Controller extends Controller
{
    public function index(){
        $bahans = Bahan::where('status_hapus', '=', 0)->orderBy('nama_bahan', 'ASC')->get();

        if(count($bahans) > 0){
            return response([
                'message' => 'Tampil Semua Bahan Berhasil',
                'data' => $bahans
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $bahan = Bahan::find($id);

        if(!is_null($bahan)){
            return response([
                'message' => 'Tampil Bahan Berhasil',
                'data' => $bahan
            ],200);
        }

        return response([
            'message' => 'Bahan Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [            
            'nama_bahan' => 'required|max:30',            
            'unit_bahan' => 'required|max:20',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        
        
        $store_data['status_hapus'] = 0;
        $store_data['stok_bahan'] = 0;

        $bahan = Bahan::create($store_data);
        return response([
            'message' => 'Tambah Bahan Berhasil',
            'data' => $bahan,
        ],200);
    }

    public function update(Request $request, $id){
        $bahan = Bahan::find($id);

        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'nama_bahan' => 'required|max:30',            
            'unit_bahan' => 'required|max:20', 
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $bahan->nama_bahan = $update_data['nama_bahan'];        
        $bahan->unit_bahan = $update_data['unit_bahan'];
 
        if($bahan->save()){
             return response([
                 'message' => 'Ubah Bahan Berhasil',
                 'data' => $bahan,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Bahan Gagal',
            'data' => null,
            ],400);
    }

    public function destroy($id){
        $bahan = Bahan::find($id);
 
        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Tidak Ditemukan',
                'data' => null
            ],404);
        }
        
        $bahan->status_hapus = 1;

        if($bahan->save()){
            return response([
                'message' => 'Hapus Bahan Berhasil',
                'data' => $bahan,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Bahan Gagal',
            'data' => null,
        ],400);
    }
}
