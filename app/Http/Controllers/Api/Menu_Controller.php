<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;
use App\Menu;
use App\Bahan;
use App\Pesanan;

class Menu_Controller extends Controller
{
    public function index(){        
        $menus = Menu::join('bahan', 'menu.id_bahan', '=', 'bahan.id')
            ->select('menu.id', 'menu.id_bahan', 'bahan.nama_bahan', 'menu.nama_menu', 'menu.deskripsi_menu',
                'menu.unit_menu', 'menu.harga_menu', 'menu.tipe_menu', 'menu.stok_menu', 'menu.gambar_menu',
                'menu.serving_size', 'menu.status_hapus', 'bahan.unit_bahan')->where('menu.status_hapus', '=', 0)
            ->orderBy('menu.tipe_menu', 'ASC')->get();

        if(count($menus) > 0){
            return response([
                'message' => 'Tampil Semua Menu Berhasil',
                'data' => $menus
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $menu = Menu::find($id);

        if(!is_null($menu)){
            return response([
                'message' => 'Tampil Menu Berhasil',
                'data' => $menu
            ],200);
        }

        return response([
            'message' => 'Menu Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [            
            'id_bahan' => 'required',
            'nama_menu' => 'required|max:30',
            'deskripsi_menu' => 'required|max:255',
            'unit_menu' => 'required|max:20',
            'harga_menu' => 'required|numeric',
            'tipe_menu' => 'required|max:20',            
            'gambar_menu' => 'required',
            'serving_size' => 'required|numeric',            
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);
        
        $bahan = Bahan::find($store_data['id_bahan']);
        // if(is_null($bahan)){
        //     return response([
        //         'message' => 'Bahan Tidak Ditemukan',
        //         'data' => null
        //     ],404);
        // }

        $unique = Menu::where('status_hapus', '=', 0)->where('nama_menu', '=', $store_data['nama_menu'])->first();
        
        if ($unique != null) {
            return response([
                'message' => 'Menu Sudah Ada',
                'data' => null,
                ],400);
        }

        $store_data['stok_menu'] = $bahan->stok_bahan / $store_data['serving_size'] ;
        $store_data['status_hapus'] = 0;        

        $menu = Menu::create($store_data);
        $bahan->id_menu = $menu->id;
        $bahan->save();
        return response([
            'message' => 'Tambah Menu Berhasil',
            'data' => $menu,
        ],200);
    }

    public function update(Request $request, $id){
        $menu = Menu::find($id);

        if(is_null($menu)){
            return response([
                'message' => 'Menu Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'id_bahan' => 'required',
            'nama_menu' => 'required|max:30',
            'deskripsi_menu' => 'required|max:255',
            'unit_menu' => 'required|max:20',
            'harga_menu' => 'required|numeric',
            'tipe_menu' => 'required|max:20',            
            'gambar_menu' => 'required',
            'serving_size' => 'required|numeric', 
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);

        $bahan = Bahan::find($update_data['id_bahan']);
        // if(is_null($bahan)){
        //     return response([
        //         'message' => 'Bahan Tidak Ditemukan',
        //         'data' => null
        //     ],404);
        // }

        $unique = Menu::where('status_hapus', '=', 0)->where('nama_menu', '=', $update_data['nama_menu'])->first();
        
        if ($unique != null) {
            if ($unique->id != $menu->id ) {
                return response([
                    'message' => 'Menu Sudah Ada',
                    'data' => null,
                    ],400);
            }                     
        }
  
        $menu->id_bahan = $update_data['id_bahan'];    
        $menu->nama_menu = $update_data['nama_menu'];
        $menu->deskripsi_menu = $update_data['deskripsi_menu'];
        $menu->unit_menu = $update_data['unit_menu'];
        $menu->harga_menu = $update_data['harga_menu'];
        $menu->tipe_menu = $update_data['tipe_menu'];
        $menu->gambar_menu = $update_data['gambar_menu'];
        $menu->serving_size = $update_data['serving_size'];

        $menu->stok_menu = $bahan->stok_bahan / $menu->serving_size ;
        
        if($menu->save()){
             return response([
                 'message' => 'Ubah Menu Berhasil',
                 'data' => $menu,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Menu Gagal',
            'data' => null,
            ],400);
    }

    public function destroy($id){
        $menu = Menu::find($id);        
 
        if(is_null($menu)){
            return response([
                'message' => 'Menu Tidak Ditemukan',
                'data' => null
            ],404);
        }
        
        if ($menu->stok_menu != 0) {
            return response([
                'message' => 'Menu harus kosong',
                'data' => null
            ],404);
        }

        if ($menu->id_bahan != null) {
            $bahan = Bahan::find($menu->id_bahan);
            $bahan->status_hapus = 1;
            $bahan->save();
        }
        
        $menu->status_hapus = 1;        

        if($menu->save()){            
            return response([
                'message' => 'Hapus Menu Berhasil',
                'data' => $menu,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Menu Gagal',
            'data' => null,
        ],400);
    }

    public function laporanPenjualanOne($dt){

        $menus = Menu::where('menu.status_hapus', '=', 0)->get();
        $size = $menus->count();
        $Shari = Carbon::parse($dt)->daysInMonth;
        // $Shari = Carbon::parse($dt)->daysInYear;
        $hari = (int)$Shari;
        $thn = Carbon::parse($dt)->format('Y');
        $bln = Carbon::parse($dt)->format('m');

        for ($i=1; $i <= $size; $i++) { 
            
            $total[$i] = 0;
            $item = [];
            $num[] = 0;
            
            for ($j=1; $j <= $hari; $j++) {
                if ($j >= 10) {
                    $date = $thn.'-'.$bln.'-'.$j;
                } else {
                    $date = $thn.'-'.$bln.'-0'.$j;
                }
                                
                $item[$j] = DB::table('pesanan')->join('menu', 'pesanan.id_menu', '=', 'menu.id')
                    ->selectRaw('ifnull(sum(pesanan.jml_pesanan), 0) as perHari')
                    ->where('menu.nama_menu', '=', $menus[$i-1]->nama_menu)
                    ->where('menu.status_hapus', '=', 0)                    
                    ->whereDate('pesanan.created_at', '=', $date)
                    ->first();

                $total[$i] = $total[$i] + $item[$j]->perHari; 
            }  
            
            $max = max($item);

            if ($menus[$i-1]->tipe_menu == 'Makanan Utama') {
                $num[0] =  $num[0] + 1;
                $no = $num[0];
            } else if ($menus[$i-1]->tipe_menu == 'Minuman') {
                $num[1] =  $num[1] + 1;
                $no = $num[1];
            } else {
                $num[2] =  $num[2] + 1;
                $no = $num[2];
            }

            $laporan[$i] = array( 
                "no" => $no,               
                "item_menu" => $menus[$i-1]->nama_menu,
                "unit" => $menus[$i-1]->unit_menu,
                "penjualan_harian_tertinggi" => $max->perHari,                
                "total_penjualan" => $total[$i],
                "tipe" => $menus[$i-1]->tipe_menu
            );            
        }
        
        return response([
            'message' => 'Tampil laporan penjualan item menu berhasil',
            'data' => $laporan,
            ],200);       
    }

    public function laporanPenjualanAll($dt){

        $menus = Menu::where('menu.status_hapus', '=', 0)->get();
        $size = $menus->count();
                                        
        for ($i=1; $i <= $size; $i++) {
            
            $total[$i] = 0;
            $item = [];
            $max = [];
            $num[] = 0;

            for ($m=1; $m <= 12; $m++) {                

                $thn = Carbon::parse($dt)->format('Y');
                if ($m >= 10) {
                    $date = $thn.'-'.$m;
                } else {
                    $date = $thn.'-0'.$m;
                }
                $Shari = Carbon::parse($date)->daysInMonth;
                $hari = (int)$Shari;

                for ($j=1; $j <= $hari; $j++) {
                    if ($j >= 10) {
                        $tgl = $date.'-'.$j;
                    } else {
                        $tgl = $date.'-0'.$j;
                    }
                                    
                    $item[$j] = DB::table('pesanan')->join('menu', 'pesanan.id_menu', '=', 'menu.id')
                        ->selectRaw('ifnull(sum(pesanan.jml_pesanan), 0) as perHari')
                        ->where('menu.nama_menu', '=', $menus[$i-1]->nama_menu)
                        ->where('menu.status_hapus', '=', 0)                    
                        ->whereDate('pesanan.created_at', '=', $tgl)
                        ->first();
    
                    $total[$i] = $total[$i] + $item[$j]->perHari; 
                }                

                $max[$m] = max($item);               
            }

            $tinggi = max($max);

            if ($menus[$i-1]->tipe_menu == 'Makanan Utama') {
                $num[0] = $num[0] + 1;
                $no = $num[0];
            } else if ($menus[$i-1]->tipe_menu == 'Minuman') {
                $num[1] = $num[1] + 1;
                $no = $num[1];
            } else {
                $num[2] = $num[2] + 1;
                $no = $num[2];
            }

            $laporan[$i] = array( 
                "no" => $no,               
                "item_menu" => $menus[$i-1]->nama_menu,
                "unit" => $menus[$i-1]->unit_menu,
                "penjualan_harian_tertinggi" => $tinggi->perHari,                
                "total_penjualan" => $total[$i],
                "tipe" => $menus[$i-1]->tipe_menu
            );  
        }
        
        return response([
            'message' => 'Tampil laporan penjualan item menu berhasil',
            'data' => $laporan,
            ],200);       
    }
}
