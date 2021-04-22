<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Customer;

class Customer_Controller extends Controller
{
    public function index(){
        $customers = Customer::where('status_hapus', '=', 0)->orderBy('nama_customer', 'ASC')->get();;

        if(count($customers) > 0){
            return response([
                'message' => 'Tampil Semua Customer Berhasil',
                'data' => $customers
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $customer = Customer::find($id);

        if(!is_null($customer)){
            return response([
                'message' => 'Tampil Customer Berhasil',
                'data' => $customer
            ],200);
        }

        return response([
            'message' => 'Customer Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'nama_customer' => 'required|max:50',
            // 'telp_customer' => 'digits_between:10,12',
            // 'email_customer' => 'email:rfc,dns',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        
        
        $uniqueNama = Customer::where('status_hapus', '=', 0)->where('nama_customer', '=', $store_data['nama_customer'])->first();
        $uniqueTelp = Customer::where('status_hapus', '=', 0)->where('telp_customer', '=', $store_data['telp_customer'])->first();
        $uniqueEmail = Customer::where('status_hapus', '=', 0)->where('email_customer', '=', $store_data['email_customer'])->first();
    
        if ($uniqueNama != null || $uniqueTelp != null || $uniqueEmail != null) {
            return response([
                'message' => 'Nama Atau No. Telp Atau Email Sudah Ada',
                'data' => null,
                ],400);
        }

        if ($store_data['telp_customer'] == 'null') {
            $store_data['telp_customer'] = null;
        }
        if ($store_data['email_customer'] == 'null') {
            $store_data['email_customer'] = null;
        }
        
        $store_data['status_hapus'] = 0;

        $customer = Customer::create($store_data);
        return response([
            'message' => 'Tambah Customer Berhasil',
            'data' => $customer,
        ],200);
    }

    public function update(Request $request, $id){
        $customer = Customer::find($id);

        if(is_null($customer)){
            return response([
                'message' => 'Customer Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'nama_customer' => 'required|max:50',
            // 'telp_customer' => 'digits_between:10,12',
            // 'email_customer' => 'email:rfc,dns', 
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);

        $uniqueNama = Customer::where('status_hapus', '=', 0)->where('nama_customer', '=', $update_data['nama_customer'])->first();
        $uniqueTelp = Customer::where('status_hapus', '=', 0)->where('telp_customer', '=', $update_data['telp_customer'])->first();
        $uniqueEmail = Customer::where('status_hapus', '=', 0)->where('email_customer', '=', $update_data['email_customer'])->first();
                
        if (($uniqueNama != null) || ($uniqueTelp != null) || ($uniqueEmail != null)) {
            $count = 0;
            if ($uniqueNama != null) {
                if ($uniqueNama->id != $customer->id ) {
                    $count++;
                }                     
            }
            else if ($uniqueTelp != null) {
                if ($uniqueTelp->id != $customer->id ) {
                    $count++;
                }                     
            }
            else if ($uniqueEmail != null) {
                if ($uniqueEmail->id != $customer->id ) {
                    $count++;
                }                     
            }

            if ($count > 0) {
                return response([
                    'message' => 'Nama Atau No. Telp Atau Email Sudah Ada',
                    'data' => null,
                    ],400);
            }                   
        }     
  
        $customer->nama_customer = $update_data['nama_customer'];
        $customer->telp_customer = $update_data['telp_customer'];
        $customer->email_customer = $update_data['email_customer'];
 
        if($customer->save()){
             return response([
                 'message' => 'Ubah Customer Berhasil',
                 'data' => $customer,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Customer Gagal',
            'data' => null,
            ],400);
    }

    public function destroy($id){
        $customer = Customer::find($id);
 
        if(is_null($customer)){
            return response([
                'message' => 'Customer Tidak Ditemukan',
                'data' => null
            ],404);
        }
        
        $customer->status_hapus = 1;

        if($customer->save()){
            return response([
                'message' => 'Hapus Customer Berhasil',
                'data' => $customer,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Customer Gagal',
            'data' => null,
        ],400);
    }
}
