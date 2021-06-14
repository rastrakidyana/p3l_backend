<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Pesanan;
use App\Histori_Bahan_Masuk;
use App\Transaksi;
use App\Menu;
use App\Bahan;

class Pesanan_Controller extends Controller
{
    public function index(){
        $pesanans = Pesanan::join('transaksi', 'pesanan.id_transaksi', '=', 'transaksi.id')
            ->join('menu', 'pesanan.id_menu', '=', 'menu.id')                        
            ->select('pesanan.id', 'pesanan.id_transaksi', 'transaksi.no_transaksi', 'pesanan.id_menu', 'menu.nama_menu', 'menu.unit_menu',
                'pesanan.jml_pesanan', 'pesanan.total_pesanan', 'pesanan.status_pesanan')
            // ->where('reservasi.status_hapus', '=', 0)
            ->orderBy('pesanan.created_at', 'DESC')->get();

        if(count($pesanans) > 0){
            return response([
                'message' => 'Tampil Semua Pesanan Berhasil',
                'data' => $pesanans
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function indexOneTransaksi($id){
        // $pesanan = Pesanan::where('id_transaksi', '=', $id)->orderBy('created_at', 'ASC')->get();
        $pesanans = Pesanan::join('menu', 'pesanan.id_menu', '=', 'menu.id')                        
            ->select('pesanan.id', 'pesanan.id_transaksi', 'pesanan.id_menu', 'menu.nama_menu', 'menu.unit_menu',
                'pesanan.jml_pesanan', 'pesanan.total_pesanan', 'pesanan.status_pesanan')
            ->where('pesanan.id_transaksi', '=', $id)
            ->orderBy('pesanan.created_at', 'ASC')->get();

        // $pesanans = DB::table('pesanan')
        //                 ->join('menu', 'pesanan.id_menu', '=', 'menu.id')
        //                 ->select(DB::raw('pesanan.id, pesanan.id_transaksi, pesanan.id_menu, menu.nama_menu, menu.unit_menu,
        //                     pesanan.jml_pesanan, pesanan.total_pesanan, pesanan.status_pesanan, count(pesanan.id) as qty'))
        //                 ->where('pesanan.id_transaksi', '=', $id)                                                
        //                 ->orderBy('pesanan.created_at', 'ASC')->get();   

        if(count($pesanans) > 0){
            $transaksi = Transaksi::find($id);
            $jumlah = 0;
            foreach ($pesanans as $pesanan) {
                $jumlah = $jumlah + $pesanan->total_pesanan;
            }
            $service = $jumlah * 0.05;
            $tax = $jumlah * 0.1;
            $transaksi->total_transaksi = $jumlah + $service + $tax;
            $transaksi->save();
            return response([
                'message' => 'Tampil Semua Pesanan Berhasil',
                'data' => $pesanans
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function sumSubTotal($id){
        $pesanans = Pesanan::join('menu', 'pesanan.id_menu', '=', 'menu.id')                        
            ->select('pesanan.id', 'pesanan.id_transaksi', 'pesanan.id_menu', 'menu.nama_menu', 'menu.unit_menu',
                'pesanan.jml_pesanan', 'pesanan.total_pesanan', 'pesanan.status_pesanan')
            ->where('pesanan.id_transaksi', '=', $id)
            ->orderBy('pesanan.created_at', 'ASC')->get();

        if(count($pesanans) > 0){
            $transaksi = Transaksi::find($id);
            $jumlah = 0;
            foreach ($pesanans as $pesanan) {
                $jumlah = $jumlah + $pesanan->total_pesanan;
            }
            return response([
                'message' => 'Subtotal transaksi',
                'data' => $jumlah,
            ],200);
        }
    }

    public function show($id){
        $pesanan = Pesanan::find($id);

        if(!is_null($pesanan)){
            return response([
                'message' => 'Tampil Pesanan Berhasil',
                'data' => $pesanan
            ],200);
        }

        return response([
            'message' => 'Pesanan Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [
            'id_transaksi' => 'required',
            'id_menu' => 'required',
            'jml_pesanan' => 'required',            
            'total_pesanan' => 'required',            
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);       
                        
        $store_data['status_pesanan'] = 'Sedang dibuatkan';

        $pesanan = Pesanan::create($store_data); 

        // $transaksi = Transaksi::find($pesanan->id_transaksi);
        // $transaksi = Transaksi::find($store_data['id_transaksi']);
        $menu = Menu::find($pesanan->id_menu);
        $bahan = Bahan::find($menu->id_bahan);
        $masuks = Histori_Bahan_Masuk::where('id_bahan', '=', $bahan->id)->where('status_hapus', '=', 0)->get();

        // $transaksi->total_transaksi = $transaksi->total_transaksi + $pesanan->total_pesanan;
        // $transaksi->total_transaksi = $transaksi->total_transaksi + $store_data['total_pesanan'];
        // $transaksi->save();
        $jml = $pesanan->jml_pesanan * $menu->serving_size;
        $bahan->stok_bahan = $bahan->stok_bahan - $jml;
        $bahan->save();
        $menu->stok_menu = $bahan->stok_bahan / $menu->serving_size;
        $menu->save();
        foreach ($masuks as $masuk) {
            $masuk->status_hapus = 2;
            $masuk->save();
        }
        
        return response([
            'message' => 'Tambah Pesanan Berhasil',
            'data' => $pesanan,
        ],200);
    }

    public function update(Request $request, $id){
        $pesanan = Pesanan::find($id);

        if(is_null($pesanan)){
            return response([
                'message' => 'Pesanan Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [ 
            'status_pesanan' => 'required',
        ]);
 
        if($validate->fails())
            return response(['message' => $validate->errors()],400);        
  
        $pesanan->status_pesanan = $update_data['status_pesanan'];     
 
        if($pesanan->save()){
            return response([
                'message' => 'Ubah Pesanan Berhasil',
                'data' => $pesanan,
                ],200);
        }
 
        return response([
            'message' => 'Ubah Pesanan Gagal',
            'data' => null,
            ],400);
    }

}
