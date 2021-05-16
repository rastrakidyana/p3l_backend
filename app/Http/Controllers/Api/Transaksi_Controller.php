<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use App\Transaksi;
use App\Reservasi;
use App\Kartu;
use App\Karyawan;

class Transaksi_Controller extends Controller
{
    public function index(){        
        $transaksis = Transaksi::join('reservasi', 'transaksi.id_reservasi', '=', 'reservasi.id')
            ->leftjoin('kartu', 'transaksi.id_kartu', '=', 'kartu.id')
            ->join('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id')
            ->join('customer', 'reservasi.id_customer', '=', 'customer.id')
            ->join('meja', 'reservasi.id_meja', '=', 'meja.id')
            ->select('transaksi.id', 'transaksi.id_reservasi', 'reservasi.tgl_reservasi', 'customer.nama_customer', 
                'meja.no_meja', 'transaksi.id_kartu', 'kartu.no_kartu', 'kartu.nama_kartu', 'kartu.tgl_kadaluarsa',
                'transaksi.id_karyawan', 'karyawan.nama_karyawan', 'transaksi.no_transaksi',
                'transaksi.total_transaksi', 'transaksi.metode_pembayaran', 'transaksi.kode_edc', 'transaksi.status_transaksi')
            ->where('status_transaksi', '!=', 'Hapus')
            ->orderBy('transaksi.created_at', 'DESC')->get();

        if(count($transaksis) > 0){
            return response([
                'message' => 'Tampil Semua Transaksi Berhasil',
                'data' => $transaksis
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        // $transaksi = Transaksi::find($id);
        $transaksi = Transaksi::join('reservasi', 'transaksi.id_reservasi', '=', 'reservasi.id')
        ->leftjoin('kartu', 'transaksi.id_kartu', '=', 'kartu.id')
        ->join('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id')
        ->join('customer', 'reservasi.id_customer', '=', 'customer.id')
        ->join('meja', 'reservasi.id_meja', '=', 'meja.id')
        ->select('transaksi.id', 'transaksi.id_reservasi', 'reservasi.tgl_reservasi', 'customer.nama_customer', 
            'meja.no_meja', 'transaksi.id_kartu', 'kartu.no_kartu', 'kartu.nama_kartu', 'kartu.tgl_kadaluarsa',
            'transaksi.id_karyawan', 'karyawan.nama_karyawan', 'transaksi.no_transaksi',
            'transaksi.total_transaksi', 'transaksi.metode_pembayaran', 'transaksi.kode_edc', 'transaksi.status_transaksi')
        ->where('status_transaksi', '!=', 'Hapus')->where('transaksi.id', '=', $id)
        ->first();

        if(!is_null($transaksi)){
            return response([
                'message' => 'Tampil Transaksi Berhasil',
                'data' => $transaksi
            ],200);
        }

        return response([
            'message' => 'Transaksi Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store($id){
        $reservasi = Reservasi::find($id);

        if($reservasi->id_transaksi != null){            
            return response([
                'message' => 'Transaksi sudah ada'
            ],404);
        }

        $store_data = [];        
                
        $store_data['id_kartu'] = null;    
        $store_data['id_karyawan'] = $reservasi->id_karyawan;
        $store_data['id_reservasi'] = $reservasi->id;
        $store_data['no_transaksi'] = $this->no_transaksi($reservasi);
        $store_data['total_transaksi'] = 0;
        $store_data['metode_pembayaran'] = null;
        $store_data['kode_edc'] = null;
        $store_data['status_transaksi'] = 'Belum Bayar';

        $transaksi = Transaksi::create($store_data);
        $reservasi->id_transaksi = $transaksi->id;
        $reservasi->save();        
        return response([
            'message' => 'Tambah Transaksi Berhasil',
            'data' => $transaksi,
        ],200);
    }

    public function update(Request $request, $id){
        $transaksi = Transaksi::find($id);

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [ 
            'id_karyawan' => 'required',        
            'metode_pembayaran' => 'required',   
            // 'id_kartu' => 'required',
        ]);
 
        if($validate->fails())
            return response(['message' => $validate->errors()],400);        
  
        $transaksi->id_karyawan = $update_data['id_karyawan'];
        $transaksi->metode_pembayaran = $update_data['metode_pembayaran'];         
        $transaksi->status_transaksi = 'Sudah Bayar';

        if ($transaksi->metode_pembayaran != 'Cash') {
            $transaksi->kode_edc = Str::random(5);            
            $transaksi->id_kartu = $update_data['id_kartu']; 
        }
 
        if($transaksi->save()){
            return response([
                'message' => 'Transaksi Selesai',
                'data' => $transaksi,
                ],200);
        }
 
        return response([
            'message' => 'Ubah Transaksi Gagal',
            'data' => null,
            ],400);
    }

    public function no_transaksi($reservasi){        

        $nomor = Transaksi::join('reservasi', 'transaksi.id_reservasi', '=', 'reservasi.id')
            ->select('transaksi.id', 'transaksi.no_transaksi')
            ->where('reservasi.tgl_reservasi', '=', $reservasi->tgl_reservasi)->get();

        $tgl = Carbon::parse($reservasi->tgl_reservasi)->format('dmy');        

        if($nomor != '[]'){
            $count = $nomor->count() + 1;
            if ($count >= 10) {
                return 'AKB-'.$tgl.'-'.$count;
            } else
                return 'AKB-'.$tgl.'-0'.$count;
        } else {
            return 'AKB-'.$tgl.'-01';
        }
    }

}
