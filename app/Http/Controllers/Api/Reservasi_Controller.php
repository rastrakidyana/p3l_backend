<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Validator;
use QrCode;
use SimpleSoftwareIO\QrCode\Generator;
use PDF;
use Carbon\Carbon;
use App\Reservasi;
use App\Meja;
use App\Customer;
use App\Karyawan;
use App\Transaksi;

class Reservasi_Controller extends Controller
{
    public function index(){
        
        $reservasis = Reservasi::join('meja', 'reservasi.id_meja', '=', 'meja.id')
            ->join('customer', 'reservasi.id_customer', '=', 'customer.id')
            ->join('karyawan', 'reservasi.id_karyawan', '=', 'karyawan.id')
            ->leftjoin('transaksi', 'reservasi.id_transaksi', '=', 'transaksi.id')
            ->select('reservasi.id', 'reservasi.id_customer', 'customer.nama_customer', 'reservasi.id_meja', 'meja.no_meja',
                'reservasi.id_karyawan', 'karyawan.nama_karyawan', 'reservasi.id_transaksi', 'transaksi.no_transaksi', 'transaksi.status_transaksi',
                'reservasi.tgl_reservasi', 'reservasi.jadwal_kunjungan', 'reservasi.kode_qr', 'reservasi.status_hapus')
            ->where('reservasi.status_hapus', '=', 0)->orderBy('reservasi.created_at', 'DESC')->get();

        if(count($reservasis) > 0){
            return response([
                'message' => 'Tampil Semua Reservasi Berhasil',
                'data' => $reservasis
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $reservasi = Reservasi::find($id);

        if(!is_null($reservasi)){
            return response([
                'message' => 'Tampil Reservasi Berhasil',
                'data' => $reservasi
            ],200);
        }

        return response([
            'message' => 'Reservasi Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'id_customer' => 'required',
            'id_meja' => 'required',
            'id_karyawan' => 'required',            
            'tgl_reservasi' => 'required',
            'jadwal_kunjungan' => 'required|max:20',                        
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);        
                
        $store_data['kode_qr'] = Str::random(10);    
        $store_data['status_hapus'] = 0;

        $reservasi = Reservasi::create($store_data);        
        return response([
            'message' => 'Tambah Reservasi Berhasil',
            'data' => $reservasi,
        ],200);
    }

    public function update(Request $request, $id){
        $reservasi = Reservasi::find($id);

        if(is_null($reservasi)){
            return response([
                'message' => 'Reservasi Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [ 
            'id_customer' => 'required',                                    
            'id_meja' => 'required',
            'id_karyawan' => 'required',        
            'tgl_reservasi' => 'required',
            'jadwal_kunjungan' => 'required|max:20',   
        ]);
 
        if($validate->fails())
            return response(['message' => $validate->errors()],400);        
  
        $reservasi->id_customer = $update_data['id_customer'];     
        $reservasi->id_meja = $update_data['id_meja'];
        $reservasi->id_karyawan = $update_data['id_karyawan'];
        $reservasi->tgl_reservasi = $update_data['tgl_reservasi'];
        $reservasi->jadwal_kunjungan = $update_data['jadwal_kunjungan'];
 
        if($reservasi->save()){
            return response([
                'message' => 'Ubah Reservasi Berhasil',
                'data' => $reservasi,
                ],200);
        }
 
        return response([
            'message' => 'Ubah Reservasi Gagal',
            'data' => null,
            ],400);
    }

    public function destroy($id){
        $reservasi = Reservasi::find($id);        
 
        if(is_null($reservasi)){
            return response([
                'message' => 'Reservasi Tidak Ditemukan',
                'data' => null
            ],404);
        }
        
        $reservasi->status_hapus = 1;        

        if($reservasi->save()){
            return response([
                'message' => 'Hapus Reservasi Berhasil',
                'data' => $reservasi,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Reservasi Gagal',
            'data' => null,
        ],400);
    }

    public function generateQRCode($id) {
        $reservasi = Reservasi::find($id);
        // $tgl = Carbon::parse($reservasi->tgl_reservasi)->format('dmy');
        // $nomor = Transaksi::join('reservasi', 'transaksi.id_reservasi', '=', 'reservasi.id')
        // ->select('transaksi.id', 'transaksi.no_transaksi')
        // ->where('reservasi.tgl_reservasi', '=', $reservasi->tgl_reservasi)->get();

        // if ($nomor == '[]') {
        //     $nomor = 2;
        // }
        $qrcode = base64_encode(QrCode::format('svg')->size(800)->errorCorrection('H')->generate($reservasi->kode_qr));

        if(!is_null($reservasi)){
            return view('qrcode.qr', compact('qrcode'));
            // return response([
            //     'message' => $tgl,
            //     'data' => null
            // ],404);
        }

        return response([
            'message' => 'Tampil QR Code gagal',
            'data' => null
        ],404);
    }

    public function pdf($id) {
        $reservasi = Reservasi::find($id);        
        $customer = Customer::find($reservasi->id_customer);
        $karyawan = Karyawan::find($reservasi->id_karyawan);
        $qrcode = base64_encode(QrCode::format('svg')->size(400)->errorCorrection('H')->generate($reservasi->kode_qr));
        $orang = $karyawan->nama_karyawan;
        $date = Carbon::parse($reservasi->tgl_reservasi)->format('M d');
        $thn = Carbon::parse($reservasi->tgl_reservasi)->format('Y');
        $jam = Carbon::now()->toTimeString();
        $pdf = PDF::loadView('qrcode.qr', compact('qrcode', 'orang', 'date', 'thn', 'jam'));
        
        return $pdf->download($reservasi->tgl_reservasi.'_'.$customer->nama_customer.'.pdf');        
    }
    
    public function scanQRCode($kode) {
        $reservasi = Reservasi::where('kode_qr', '=', $kode)->first();
        $transaksi = Transaksi::where('id_reservasi', '=', $reservasi->id)
                                    ->where('status_transaksi', '=', 'Belum Bayar')->first();
        
        if(!is_null($transaksi)){
            return response([
                'message' => 'Scan QR Code Berhasil',
                'data' => $transaksi
            ],200);
        }

        return response([
            'message' => 'Scan QR Code gagal',
            'data' => null
        ],200);
    }

}
