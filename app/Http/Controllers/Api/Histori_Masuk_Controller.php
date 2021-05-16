<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;
use App\Histori_Bahan_Masuk;
use App\Bahan;
use App\Menu;

class Histori_Masuk_Controller extends Controller
{
    public function index(){
        // $dt = Carbon::today()->toDateString();
        // $ins = Histori_Bahan_Masuk::where('status_hapus', '!=', 1)->where('status_hapus', '!=', 1)->get();

        $masuks = Histori_Bahan_Masuk::join('bahan', 'histori__bahan__masuk.id_bahan', '=', 'bahan.id')
            ->select('histori__bahan__masuk.id', 'histori__bahan__masuk.id_bahan', 'bahan.nama_bahan', 'bahan.unit_bahan',
                'histori__bahan__masuk.jml_masuk', 'histori__bahan__masuk.tgl_masuk', 'histori__bahan__masuk.harga_bahan',                
                'histori__bahan__masuk.status_hapus')->where('histori__bahan__masuk.status_hapus', '!=', 1)
            ->orderBy('histori__bahan__masuk.created_at', 'DESC')->get();

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
            
        $bahan = Bahan::find($store_data['id_bahan']);

        $bahan->stok_bahan = $bahan->stok_bahan + $store_data['jml_masuk'];  
        
        if ($bahan->id_menu != null) {
            $menu = Menu::find($bahan->id_menu);
            $menu->stok_menu = $bahan->stok_bahan / $menu->serving_size;
            $menu->save();
        }

        $store_data['status_hapus'] = 0;

        $masuk = Histori_Bahan_Masuk::create($store_data);
        $bahan->save();        
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

        $bahanBaru = Bahan::where('id', '=', $update_data['id_bahan'])->first();
        $bahanLama = Bahan::where('id', '=', $masuk->id_bahan)->first();
        $menuBaru = Menu::where('id', '=', $bahanBaru->id_menu)->first();
        $menuLama = Menu::where('id', '=', $bahanLama->id_menu)->first();
 
        if ($bahanBaru->id != $bahanLama->id) {
            $bahanBaru->stok_bahan = $bahanBaru->stok_bahan + $update_data['jml_masuk'];
            $bahanLama->stok_bahan = $bahanLama->stok_bahan - $masuk->jml_masuk;
            $bahanBaru->save();            
        } else {
            if ($masuk->jml_masuk != $update_data['jml_masuk']) {
                $bahanLama->stok_bahan = $bahanLama->stok_bahan - $masuk->jml_masuk;
                $bahanLama->stok_bahan = $bahanLama->stok_bahan + $update_data['jml_masuk'];                                
            }
        }

        $bahanLama->save();
        $masuk->id_bahan = $update_data['id_bahan'];        
        $masuk->jml_masuk = $update_data['jml_masuk'];
        $masuk->tgl_masuk = $update_data['tgl_masuk'];
        $masuk->harga_bahan = $update_data['harga_bahan'];
        
        if ($menuLama != null) {
            $menuLama->stok_menu = $bahanLama->stok_bahan / $menuLama->serving_size;
            $menuLama->save();
        }

        if ($menuBaru != null) {
            $menuBaru->stok_menu = $bahanBaru->stok_bahan / $menuBaru->serving_size;
            $menuBaru->save();
        }
 
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

    public function destroy($id){
        $masuk = Histori_Bahan_Masuk::find($id);
 
        if(is_null($masuk)){
            return response([
                'message' => 'Histori Bahan Masuk Tidak Ditemukan',
                'data' => null
            ],404);
        }
        
        $bahan = Bahan::where('id', '=', $masuk->id_bahan)->first();
        $menu = Menu::where('id', '=', $bahan->id_menu)->first();

        $bahan->stok_bahan = $bahan->stok_bahan - $masuk->jml_masuk;
        if ($menu != null) {
            $menu->stok_menu = $bahan->stok_bahan / $menu->serving_size;
            $menu->save();
        }
        $bahan->save();

        $masuk->status_hapus = 1;

        if($masuk->save()){
            return response([
                'message' => 'Hapus Histori Bahan Masuk Berhasil',
                'data' => $masuk,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Histori Bahan Masuk Gagal',
            'data' => null,
        ],400);
    }
}
