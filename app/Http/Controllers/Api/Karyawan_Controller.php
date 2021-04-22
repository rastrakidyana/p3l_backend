<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Karyawan;
use App\Jabatan;
use Validator;

class Karyawan_Controller extends Controller
{
    public function index(){
        $karyawans = Karyawan::join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id')
            ->select('karyawan.id', 'karyawan.id_jabatan', 'jabatan.nama_jabatan', 'karyawan.nama_karyawan', 'karyawan.jk_karyawan',
                'karyawan.telp_karyawan', 'karyawan.email', 'karyawan.tgl_gabung_karyawan', 'karyawan.status_karyawan')
            ->orderBy('karyawan.status_karyawan', 'ASC')->orderBy('karyawan.nama_karyawan', 'ASC')->get();

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
  
        if ($karyawan->tgl_gabung_karyawan == $karyawan->password) {
            $karyawan->password = $update_data['tgl_gabung_karyawan'];
        }

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

    public function changePass(Request $request){
        $karyawan = Auth::user();

        if(is_null($karyawan)){
            return response([
                'message' => 'User Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        if (Hash::check($request->old_pass, $karyawan->password)) {
            $validate = Validator::make($update_data, [
                'new_pass' => 'required',                
            ]);
        } else {
            return response(['message' => 'Password lama salah'],400);
        }        
 
        if($validate->fails())
            return response(['message' => $validate->errors()],400);
  
        $karyawan->password = bcrypt($update_data['new_pass']);       
 
        if($karyawan->save()){
            return response([
                'message' => 'Ubah Password Berhasil',
                'data' => $karyawan,
                ],200);
        }
 
        return response([
            'message' => 'Ubah Password Gagal',
            'data' => null,
            ],400);
    }

    public function status($id){
        $karyawan = Karyawan::find($id);
        if(is_null($karyawan)){
            return response([
                'message' => 'Karyawan Tidak Ditemukan',
                'data' => null
            ],404);
        }

        if($karyawan->status_karyawan == 'Aktif') {
            $karyawan->status_karyawan = 'Nonaktif';

            if($karyawan->save()){
                return response([
                    'message' => 'Karyawan Dinonaktifkan Berhasil',
                    'data' => $karyawan,
                    ],200);
            }
        }elseif ($karyawan->status_karyawan == 'Nonaktif') {
            $karyawan->status_karyawan = 'Aktif';

            if($karyawan->save()){
                return response([
                    'message' => 'Karyawan Diaktifkan Berhasil',
                    'data' => $karyawan,
                    ],200);
            }
        }        

        return response([
            'message' => 'Ubah Status Karyawan Gagal',
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
            return response(['message' => 'E-mail atau Password salah'],401);
    
        $karyawan = Auth::user();

        if ($karyawan->status_karyawan == 'Nonaktif') {
            return response([
                'message' => 'Login Gagal',
                'karyawan' => null,
            ],400);
        }

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
