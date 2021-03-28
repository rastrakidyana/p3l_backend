<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Reservasi;

class Reservasi_Controller extends Controller
{
    public function index(){
        $reservasis = Reservasi::all();

        if(count($reservasis) > 0){
            return response([
                'message' => 'Tampil Semua Reservasi Berhasil',
                'data' => $reservasis
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $reservasi = Reservasi::find($id);

        if(!is_null($reservasi)){
            return response([
                'message' => 'Tampil Reservasi Berhasil',
                'data' => $reservasi
            ],200);
        }

        return response([
            'message' => 'Reservasi Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'id_customer' => 'required',
            'id_meja' => 'required',
            'id_karyawan' => 'required',            
            'tgl_reservasi' => 'required',
            'jadwal_kunjungan' => 'required|max:20',                        
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        
        
        $store_data['kode_qr'] = 0;    
        $store_data['status_hapus'] = 0;

        $reservasi = Reservasi::create($store_data);
        return response([
            'message' => 'Tambah Reservasi Berhasil',
            'data' => $reservasi,
        ],200);
    }

    public function update(Request $request, $id){
        $reservasi = Reservasi::find($id);

        if(is_null($reservasi)){
            return response([
                'message' => 'Reservasi Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [            
            'id_meja' => 'required',                        
            'tgl_reservasi' => 'required',
            'jadwal_kunjungan' => 'required|max:20',   
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $reservasi->id_meja = $update_data['id_meja'];
        $reservasi->tgl_reservasi = $update_data['tgl_reservasi'];
        $reservasi->jadwal_kunjungan = $update_data['jadwal_kunjungan'];
 
        if($reservasi->save()){
             return response([
                 'message' => 'Ubah Reservasi Berhasil',
                 'data' => $reservasi,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Reservasi Gagal',
            'data' => null,
            ],400);
    }

    public function destroy($id){
        $reservasi = Reservasi::find($id);
 
        if(is_null($reservasi)){
            return response([
                'message' => 'Reservasi Tidak Ditemukan',
                'data' => null
            ],404);
        }
        
        $reservasi->status_hapus = 1;

        if($reservasi->save()){
            return response([
                'message' => 'Hapus Reservasi Berhasil',
                'data' => $reservasi,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Reservasi Gagal',
            'data' => null,
        ],400);
    }
}
