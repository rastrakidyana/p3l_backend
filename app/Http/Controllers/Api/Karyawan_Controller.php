<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Karyawan;
use Validator;

class Karyawan_Controller extends Controller
{
    public function index(){
        $karyawans = Karyawan::all();

        if(count($karyawans) > 0){
            return response([
                'message' => 'Tampil Semua Karyawan Berhasil',
                'data' => $karyawans
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $karyawan = Karyawan::find($id);

        if(!is_null($karyawan)){
            return response([
                'message' => 'Tampil Karyawan Berhasil',
                'data' => $karyawan
            ],200);
        }

        return response([
            'message' => 'Karyawan Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'id_jabatan' => 'required',
            'nama_karyawan' => 'required|max:60|unique:karyawan',
            'jk_karyawan' => 'required',
            'telp_karyawan' => 'required|digits_between:10,12|unique:karyawan',
            'email' => 'required|email:rfc,dns|unique:karyawan',
            'tgl_gabung_karyawan' => 'required',                        
            'password' => 'required|max:15',            
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $store_data['status_karyawan'] = 'Aktif';
        $store_data['password'] = bcrypt($request->password);

        $karyawan = Karyawan::create($store_data);
        return response([
            'message' => 'Tambah Karyawan Berhasil',
            'data' => $karyawan,
        ],200);
    }

    public function update(Request $request, $id){
        $karyawan = Karyawan::find($id);

        if(is_null($karyawan)){
            return response([
                'message' => 'Karyawan Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'id_jabatan' => 'required',
            'nama_karyawan' => 'required|max:60',
            'jk_karyawan' => 'required',
            'telp_karyawan' => 'required|digits_between:10,12',
            'email' => 'required|email:rfc,dns',
            'tgl_gabung_karyawan' => 'required',                  
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
        $karyawan->id_jabatan = $update_data['id_jabatan'];
        $karyawan->nama_karyawan = $update_data['nama_karyawan'];
        $karyawan->jk_karyawan = $update_data['jk_karyawan'];
        $karyawan->telp_karyawan = $update_data['telp_karyawan'];
        $karyawan->email = $update_data['email'];
        $karyawan->tgl_gabung_karyawan = $update_data['tgl_gabung_karyawan'];           
 
        if($karyawan->save()){
             return response([
                 'message' => 'Ubah Karyawan Berhasil',
                 'data' => $karyawan,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Karyawan Gagal',
            'data' => null,
            ],400);
    }

    public function login(Request $request){
        $login_data = $request->all();
        $validate = Validator::make($login_data, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);
    
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
    
        if(!Auth::attempt($login_data))
            return response(['message' => $login_data],401);
    
        $karyawan = Auth::user();
        $token = $karyawan->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Login Berhasil',
            'karyawan' => $karyawan,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }
    
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Logout Berhasil'
        ]);
    }    

}
