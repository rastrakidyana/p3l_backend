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
        $customers = Customer::all();

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
            'telp_customer' => 'required|digits_between:10,12',
            'email_customer' => 'required|email:rfc,dns',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        
        
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
            'telp_customer' => 'required|digits_between:10,12',
            'email_customer' => 'required|email:rfc,dns', 
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);
  
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
