<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;
use App\Transaksi;
use App\Meja;
use App\Reservasi;

class Meja_Controller extends Controller
{
    // public function index(){
    //     $mejas = Meja::where('status_hapus', '=', 0)->get();

    //     if(count($mejas) > 0){
    //         return response([
    //             'message' => 'Tampil Semua Meja Berhasil',
    //             'data' => $mejas
    //         ],200);
    //     }

    //     return response([
    //         'message' => 'Kosong',
    //         'data' => null
    //     ],404);
    // }

    public function index(){
        // $reservasis = Reservasi::where('status_hapus', '=', 0)->get();
        $mejas = Meja::where('status_hapus', '=', 0)->get();

        foreach ($mejas as $meja) {
            $meja->status_meja = 'Tersedia';
            $meja->save();
        }        

        $dt = Carbon::today()->toDateString();
        $timeNow = Carbon::now()->toTimeString();
        // $timeNow = '13:00:00';
        $reservasis = Reservasi::where('status_hapus', '=', 0)->where('tgl_reservasi', '=', $dt)->get();

        foreach ($reservasis as $reservasi) {
            if ($timeNow >= '09:00:00' && $timeNow <= '16:00:00' && $reservasi->jadwal_kunjungan == 'Lunch') {
                $transaksi = Transaksi::where('id', '=', $reservasi->id_transaksi)->first();
                if ($transaksi == null) {
                    $mejaTT = Meja::where('status_hapus', '=', 0)->where('id', '=', $reservasi->id_meja)->first();
                    $mejaTT->status_meja = 'Tidak Tersedia';
                    $mejaTT->save();   
                } else if ($transaksi->status_transaksi == 'Belum Bayar') {
                    $mejaTT = Meja::where('status_hapus', '=', 0)->where('id', '=', $reservasi->id_meja)->first();
                    $mejaTT->status_meja = 'Tidak Tersedia';
                    $mejaTT->save();  
                }               
            } else if ($timeNow >= '16:00:01' && $timeNow <= '20:00:00' && $reservasi->jadwal_kunjungan == 'Dinner') {
                $transaksi = Transaksi::where('id', '=', $reservasi->id_transaksi)->first();
                if ($transaksi == null) {
                    $mejaT = Meja::where('status_hapus', '=', 0)->where('id', '=', $reservasi->id_meja)->first();
                    $mejaT->status_meja = 'Tidak Tersedia';
                    $mejaT->save();                    
                } else if ($transaksi->status_transaksi == 'Belum Bayar') {
                    $mejaT = Meja::where('status_hapus', '=', 0)->where('id', '=', $reservasi->id_meja)->first();
                    $mejaT->status_meja = 'Tidak Tersedia';
                    $mejaT->save();  
                }
            }
        }

        $mejas = Meja::where('status_hapus', '=', 0)->orderBy('no_meja', 'ASC')->get();

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
            
        $unique = Meja::where('status_hapus', '=', 0)->where('no_meja', '=', $store_data['no_meja'])->first();
        
        if ($unique != null) {
            return response([
                'message' => 'Meja Sudah Ada',
                'data' => null,
                ],400);
        }

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
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $unique = Meja::where('status_hapus', '=', 0)->where('no_meja', '=', $update_data['no_meja'])->first();
        
        if ($unique != null) {
            if ($unique->id != $meja->id ) {
                return response([
                    'message' => 'Meja Sudah Ada',
                    'data' => null,
                    ],400);
            }                     
        }
        
        $meja->no_meja = $update_data['no_meja'];        
 
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
