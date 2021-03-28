<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Jabatan;

class Jabatan_Controller extends Controller
{
    public function index(){
        $jabatans = Jabatan::all();

        if(count($jabatans) > 0){
            return response([
                'message' => 'Tampil Semua Jabatan Berhasil',
                'data' => $jabatans
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $jabatan = Jabatan::find($id);

        if(!is_null($jabatan)){
            return response([
                'message' => 'Tampil Jabatan Berhasil',
                'data' => $jabatan
            ],200);
        }

        return response([
            'message' => 'Jabatan Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'nama_jabatan' => 'required|max:30|unique:jabatan',                
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        

        $jabatan = Jabatan::create($store_data);
        return response([
            'message' => 'Tambah Jabatan Berhasil',
            'data' => $jabatan,
        ],200);
    }

    public function update(Request $request, $id){
        $jabatan = Jabatan::find($id);

        if(is_null($jabatan)){
            return response([
                'message' => 'Jabatan Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'nama_jabatan' => 'required|max:30',    
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $jabatan->nama_jabatan = $update_data['nama_jabatan'];
 
        if($jabatan->save()){
             return response([
                 'message' => 'Ubah Jabatan Berhasil',
                 'data' => $jabatan,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Jabatan Gagal',
            'data' => null,
            ],400);
    }
}
