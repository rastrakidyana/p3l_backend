<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;
use App\Histori_Bahan_Keluar;
use App\Pesanan;
use App\Bahan;
use App\Menu;

class Histori_Keluar_Controller extends Controller
{
    public function index(){
        $keluars = Histori_Bahan_Keluar::join('bahan', 'histori__bahan__keluar.id_bahan', '=', 'bahan.id')
            ->select('histori__bahan__keluar.id', 'histori__bahan__keluar.id_bahan', 'bahan.nama_bahan', 'bahan.unit_bahan',
                'histori__bahan__keluar.jml_keluar', 'histori__bahan__keluar.tgl_keluar', 'histori__bahan__keluar.status_keluar')
            ->orderBy('histori__bahan__keluar.created_at', 'DESC')->get();

        if(count($keluars) > 0){
            return response([
                'message' => 'Tampil Semua Histori Bahan Keluar Berhasil',
                'data' => $keluars
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $keluar = Histori_Bahan_Keluar::find($id);

        if(!is_null($keluar)){
            return response([
                'message' => 'Tampil Histori Bahan Keluar Berhasil',
                'data' => $keluar
            ],200);
        }

        return response([
            'message' => 'Histori Bahan Keluar Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store($id){
        $pesanan = Pesanan::find($id);

        if(is_null($pesanan)){
            return response([
                'message' => 'Pesanan Tidak Ditemukan',
                'data' => null
            ],404);
        }

        $menu = Menu::find($pesanan->id_menu);

        if(is_null($menu)){
            return response([
                'message' => 'Menu Tidak Ditemukan',
                'data' => null
            ],404);
        }

        $jml = $pesanan->jml_pesanan * $menu->serving_size;

        $store_data = [];        
                
        $store_data['id_bahan'] = $menu->id_bahan;    
        $store_data['jml_keluar'] = $jml;
        $store_data['tgl_keluar'] = Carbon::today()->toDateString();

        $keluar = Histori_Bahan_Keluar::create($store_data);
        return response([
            'message' => 'Tambah Histori Bahan Keluar Berhasil',
            'data' => $keluar,
        ],200);
    }

    // public function tambah(Request $request){
    //     $store_data = $request->all();
    //     $validate = Validator::make($store_data, [
    //         'id_bahan' => 'required',
    //         'jml_keluar' => 'required',
    //         'tgl_keluar' => 'required',
    //     ]);

    //     if($validate->fails())
    //         return response(['message'=> $validate->errors()],400);

    //     $store_data['status_keluar'] = 1;
            
    //     $bahan = Bahan::find($store_data['id_bahan']);

    //     if ($bahan->stok_bahan == 0) {
    //         return response([
    //             'message' => 'Tambah Histori Bahan Keluar Gagal',
    //             'data' => null,
    //         ],400);
    //     }

    //     $bahan->stok_bahan = $bahan->stok_bahan - $store_data['jml_keluar'];  
        
    //     if ($bahan->id_menu != null) {
    //         $menu = Menu::find($bahan->id_menu);
    //         $menu->stok_menu = $bahan->stok_bahan / $menu->serving_size;
    //         $menu->save();
    //     }

    //     $keluar = Histori_Bahan_Keluar::create($store_data);
    //     $bahan->save();       
    //     return response([
    //         'message' => 'Tambah Histori Bahan Keluar Berhasil',
    //         'data' => $keluar,
    //     ],200);
    // }

    // public function waste(){
    //     $timeNow = Carbon::now()->toTimeString();
    //     $dt = Carbon::today()->toDateString();
        
    //     if ($timeNow < '23:30:00' ) {
    //         return response([
    //             'message' => 'Belum waktunya membuang stok bahan',
    //             'data' => null,
    //         ],404);
    //     }

    //     $bahans = Bahan::where('status_hapus', '=', 0)->get();
        
    //     $i = 0;
    //     foreach ($bahans as $bahan) {            
    //         if ($bahan->stok_bahan > 0) {
    //             $store_data = [];
    //             $store_data['id_bahan'] = $bahan->id;
    //             $store_data['jml_keluar'] = $bahan->stok_bahan;
    //             $store_data['tgl_keluar'] = $dt;
    //             $store_data['status_keluar'] = 1;   

    //             $bahan->stok_bahan = $bahan->stok_bahan - $store_data['jml_keluar'];

    //             if ($bahan->id_menu != null) {
    //                 $menu = Menu::find($bahan->id_menu);
    //                 $menu->stok_menu = $bahan->stok_bahan / $menu->serving_size;
    //                 $menu->save();
    //             }

    //             $waste[$i] = Histori_Bahan_Keluar::create($store_data);
    //             $bahan->save();
    //             $i = $i + 1;
    //         }            
            
    //     }                                    
 
    //     return response([
    //         'message' => 'Tambah Bahan Terbuang Berhasil',
    //         'data' => $waste,
    //     ],200);
    // }

}
