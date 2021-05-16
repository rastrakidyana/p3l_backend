<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Menu;
use App\Bahan;

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
}
