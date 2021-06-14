<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;
use App\Bahan;
use App\Menu;

class Bahan_Controller extends Controller
{
    public function index(){
        $bahans = Bahan::where('status_hapus', '=', 0)->orderBy('nama_bahan', 'ASC')->get();

        if(count($bahans) > 0){
            return response([
                'message' => 'Tampil Semua Bahan Berhasil',
                'data' => $bahans
            ],200);
        }

        return response([
            'message' => 'Kosong',
            'data' => null
        ],404);
    }

    public function show($id){
        $bahan = Bahan::find($id);

        if(!is_null($bahan)){
            return response([
                'message' => 'Tampil Bahan Berhasil',
                'data' => $bahan
            ],200);
        }

        return response([
            'message' => 'Bahan Tidak Ditemukan',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $store_data = $request->all();
        $validate = Validator::make($store_data, [            
            'nama_bahan' => 'required|max:30',            
            'unit_bahan' => 'required|max:20',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);
            
        $unique = Bahan::where('status_hapus', '=', 0)->where('nama_bahan', '=', $store_data['nama_bahan'])->first();
        
        if ($unique != null) {
            return response([
                'message' => 'Bahan Sudah Ada',
                'data' => null,
                ],400);
        }
        
        $store_data['status_hapus'] = 0;
        $store_data['stok_bahan'] = 0;

        $bahan = Bahan::create($store_data);
        return response([
            'message' => 'Tambah Bahan Berhasil',
            'data' => $bahan,
        ],200);
    }

    public function update(Request $request, $id){
        $bahan = Bahan::find($id);

        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Tidak Ditemukan',
                'data' => null
            ],404);
        }
 
        $update_data = $request->all();
        $validate = Validator::make($update_data, [
            'nama_bahan' => 'required|max:30',            
            'unit_bahan' => 'required|max:20', 
        ]);
 
        if($validate->fails())
             return response(['message' => $validate->errors()],400);

        $unique = Bahan::where('status_hapus', '=', 0)->where('nama_bahan', '=', $update_data['nama_bahan'])->first();
        
        if ($unique != null) {
            if ($unique->id != $bahan->id ) {
                return response([
                    'message' => 'Bahan Sudah Ada',
                    'data' => null,
                    ],400);
            }                     
        }
  
        $bahan->nama_bahan = $update_data['nama_bahan'];        
        $bahan->unit_bahan = $update_data['unit_bahan'];
 
        if($bahan->save()){
             return response([
                 'message' => 'Ubah Bahan Berhasil',
                 'data' => $bahan,
                 ],200);
        }
 
        return response([
            'message' => 'Ubah Bahan Gagal',
            'data' => null,
            ],400);
    }

    public function destroy($id){
        $bahan = Bahan::find($id);
 
        if(is_null($bahan)){
            return response([
                'message' => 'Bahan Tidak Ditemukan',
                'data' => null
            ],404);
        }

        if ($bahan->stok_bahan != 0) {
            return response([
                'message' => 'Bahan harus kosong',
                'data' => null
            ],404);
        }
        
        if ($bahan->id_menu != null) {
            $menu = Menu::find($bahan->id_menu);
            $menu->status_hapus = 1;
            $menu->save();
        }

        $bahan->status_hapus = 1;

        if($bahan->save()){            
            return response([
                'message' => 'Hapus Bahan Berhasil',
                'data' => $bahan,
                ],200);
        }
 
        return response([
            'message' => 'Hapus Bahan Gagal',
            'data' => null,
        ],400);
    }

    public function laporanBahanCustom($dtF, $dtL){
        
        $menus = Menu::join('bahan', 'menu.id_bahan', '=', 'bahan.id')
            ->select('menu.id', 'menu.id_bahan', 'bahan.nama_bahan', 'menu.nama_menu',
                'menu.tipe_menu', 'menu.status_hapus', 'bahan.unit_bahan')->where('menu.status_hapus', '=', 0)
            ->get();
        $size = $menus->count();
        
        $from = date($dtF);
        $to = date($dtL);

        for ($i=1; $i <= $size; $i++) { 

            $num[] = 0;
                                                             
            $incoming[$i] = DB::table('histori__bahan__masuk')->join('bahan', 'histori__bahan__masuk.id_bahan', 'bahan.id')
                ->join('menu', 'menu.id_bahan', 'bahan.id')
                ->selectRaw('ifnull(sum(histori__bahan__masuk.jml_masuk), 0) as jmlMasuk')
                ->where('menu.nama_menu', '=', $menus[$i-1]->nama_menu)
                ->where('menu.status_hapus', '=', 0)
                ->where('bahan.status_hapus', '=', 0)
                ->where('histori__bahan__masuk.status_hapus', '!=', 1)
                ->whereBetween('histori__bahan__masuk.tgl_masuk', [$from, $to])              
                ->first();

            $waste[$i] = DB::table('histori__bahan__keluar')->join('bahan', 'histori__bahan__keluar.id_bahan', 'bahan.id')
                ->join('menu', 'menu.id_bahan', 'bahan.id')
                ->selectRaw('ifnull(sum(histori__bahan__keluar.jml_keluar), 0) as jmlKeluar')
                ->where('menu.nama_menu', '=', $menus[$i-1]->nama_menu)
                ->where('menu.status_hapus', '=', 0)
                ->where('bahan.status_hapus', '=', 0)
                ->where('histori__bahan__keluar.status_keluar', '=', 1)
                ->whereBetween('histori__bahan__keluar.tgl_keluar', [$from, $to])              
                ->first();

            $out[$i] = DB::table('histori__bahan__keluar')->join('bahan', 'histori__bahan__keluar.id_bahan', 'bahan.id')
                ->join('menu', 'menu.id_bahan', 'bahan.id')
                ->selectRaw('ifnull(sum(histori__bahan__keluar.jml_keluar), 0) as jmlKeluar')
                ->where('menu.nama_menu', '=', $menus[$i-1]->nama_menu)
                ->where('menu.status_hapus', '=', 0)
                ->where('bahan.status_hapus', '=', 0)
                ->where('histori__bahan__keluar.status_keluar', '=', NULL)
                ->whereBetween('histori__bahan__keluar.tgl_keluar', [$from, $to])              
                ->first();

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
                "unit" => $menus[$i-1]->unit_bahan,
                "incoming_stock" => $incoming[$i]->jmlMasuk,
                "remaining_stock" => $incoming[$i]->jmlMasuk - $out[$i]->jmlKeluar,                
                "waste_stock" => $waste[$i]->jmlKeluar,
                "tipe" => $menus[$i-1]->tipe_menu
            );            
        }
        
        return response([
            'message' => 'Tampil laporan stok bahan custom berhasil',
            'data' => $laporan,
            ],200);       
    }

    public function laporanBahanMY($item, $dt){
        
        $menu = Menu::join('bahan', 'menu.id_bahan', '=', 'bahan.id')
            ->select('menu.id', 'menu.id_bahan', 'bahan.nama_bahan', 'menu.nama_menu',
                'menu.status_hapus', 'bahan.unit_bahan')->where('menu.status_hapus', '=', 0)->where('menu.nama_menu', '=', $item)
            ->first();

        $Shari = Carbon::parse($dt)->daysInMonth;
        $hari = (int)$Shari;
        $thn = Carbon::parse($dt)->format('Y');
        $bln = Carbon::parse($dt)->format('m');

        for ($i=1; $i <= $hari; $i++) {
            if ($i >= 10) {
                $date = $thn.'-'.$bln.'-'.$i;
            } else {
                $date = $thn.'-'.$bln.'-0'.$i;
            }
              
            $incoming[$i] = DB::table('histori__bahan__masuk')->join('bahan', 'histori__bahan__masuk.id_bahan', 'bahan.id')
                ->join('menu', 'menu.id_bahan', 'bahan.id')
                ->selectRaw('ifnull(sum(histori__bahan__masuk.jml_masuk), 0) as jmlMasuk')
                ->where('menu.nama_menu', '=', $menu->nama_menu)
                ->where('menu.status_hapus', '=', 0)
                ->where('bahan.status_hapus', '=', 0)
                ->where('histori__bahan__masuk.status_hapus', '!=', 1)
                ->whereDATE('histori__bahan__masuk.tgl_masuk', '=', $date)                
                ->first();

            $waste[$i] = DB::table('histori__bahan__keluar')->join('bahan', 'histori__bahan__keluar.id_bahan', 'bahan.id')
                ->join('menu', 'menu.id_bahan', 'bahan.id')
                ->selectRaw('ifnull(sum(histori__bahan__keluar.jml_keluar), 0) as jmlKeluar')
                ->where('menu.nama_menu', '=', $menu->nama_menu)
                ->where('menu.status_hapus', '=', 0)
                ->where('bahan.status_hapus', '=', 0)
                ->where('histori__bahan__keluar.status_keluar', '=', 1)
                ->whereDATE('histori__bahan__keluar.tgl_keluar', '=', $date)         
                ->first();

            $out[$i] = DB::table('histori__bahan__keluar')->join('bahan', 'histori__bahan__keluar.id_bahan', 'bahan.id')
                ->join('menu', 'menu.id_bahan', 'bahan.id')
                ->selectRaw('ifnull(sum(histori__bahan__keluar.jml_keluar), 0) as jmlKeluar')
                ->where('menu.nama_menu', '=', $menu->nama_menu)
                ->where('menu.status_hapus', '=', 0)
                ->where('bahan.status_hapus', '=', 0)
                ->where('histori__bahan__keluar.status_keluar', '=', NULL)
                ->whereDATE('histori__bahan__keluar.tgl_keluar', '=', $date)           
                ->first();

            $rem_stok = $incoming[$i]->jmlMasuk - $out[$i]->jmlKeluar;
            if ($rem_stok < 0) {
                $rem_stok = 0;
            }
                
            $laporan[$i] = array( 
                "no" => $i,               
                "tanggal" => Carbon::parse($date)->format('d M Y'),
                "unit" => $menu->unit_bahan,
                "incoming_stock" => $incoming[$i]->jmlMasuk,
                "remaining_stock" => $rem_stok,                
                "waste_stock" => $waste[$i]->jmlKeluar
            );
        }
        
        return response([
            'message' => 'Tampil laporan stok bahan month year berhasil',
            'data' => $laporan,
            ],200);       
    }
}
