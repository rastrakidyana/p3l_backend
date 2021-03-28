<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Menu;

class Menu_Controller extends Controller
{
    public function index(){
        $menus = Menu::all();

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
            'deskripsi_menu' => 'required|max:50',
            'unit_menu' => 'required|max:20',
            'harga_menu' => 'required|numeric',
            'tipe_menu' => 'required|max:20',            
            'gambar_menu' => 'required',
            'serving_size' => 'required|numeric',            
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        
        
        $store_data['status_hapus'] = 0;

        $menu = Menu::create($store_data);
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
            'deskripsi_menu' => 'required|max:50',
            'unit_menu' => 'required|max:20',
            'harga_menu' => 'required|numeric',
            'tipe_menu' => 'required|max:20',            
            'gambar_menu' => 'required',
            'serving_size' => 'required|numeric', 
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $menu->id_bahan = $update_data['id_bahan'];    
        $menu->nama_menu = $update_data['nama_menu'];
        $menu->deskripsi_menu = $update_data['deskripsi_menu'];
        $menu->unit_menu = $update_data['unit_menu'];
        $menu->harga_menu = $update_data['harga_menu'];
        $menu->tipe_menu = $update_data['tipe_menu'];
        $menu->gambar_menu = $update_data['gambar_menu'];
        $menu->serving_size = $update_data['serving_size'];
 
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
