<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login','Api\Karyawan_Controller@login');

Route::group(['middleware' => 'auth:api'],function(){
    Route::get('karyawan', 'Api\Karyawan_Controller@index');
    Route::get('karyawan/{id}', 'Api\Karyawan_Controller@show');
    Route::post('karyawan', 'Api\Karyawan_Controller@store');
    Route::put('karyawan/{id}', 'Api\Karyawan_Controller@update');
    Route::get('karyawan_status/{id}', 'Api\Karyawan_Controller@status');
    Route::put('karyawan_change_pass', 'Api\Karyawan_Controller@changePass');

    Route::get('jabatan', 'Api\Jabatan_Controller@index');
    Route::get('jabatan/{id}', 'Api\Jabatan_Controller@show');
    Route::post('jabatan', 'Api\Jabatan_Controller@store');
    Route::put('jabatan/{id}', 'Api\Jabatan_Controller@update');

    Route::get('meja', 'Api\Meja_Controller@index');
    Route::get('meja/{id}', 'Api\Meja_Controller@show');
    Route::post('meja', 'Api\Meja_Controller@store');
    Route::put('meja/{id}', 'Api\Meja_Controller@update');
    Route::get('meja_hapus/{id}', 'Api\Meja_Controller@destroy');

    Route::get('customer', 'Api\Customer_Controller@index');
    Route::get('customer/{id}', 'Api\Customer_Controller@show');
    Route::post('customer', 'Api\Customer_Controller@store');
    Route::put('customer/{id}', 'Api\Customer_Controller@update');
    Route::get('customer_hapus/{id}', 'Api\Customer_Controller@destroy');

    Route::get('reservasi', 'Api\Reservasi_Controller@index');
    Route::get('reservasi/{id}', 'Api\Reservasi_Controller@show');
    Route::post('reservasi', 'Api\Reservasi_Controller@store');
    Route::put('reservasi/{id}', 'Api\Reservasi_Controller@update');
    Route::get('reservasi_hapus/{id}', 'Api\Reservasi_Controller@destroy');
    Route::get('reservasi_pdf/{id}', 'Api\Reservasi_Controller@pdf');
    
    
    Route::get('transaksi', 'Api\Transaksi_Controller@index');
    Route::get('transaksi/{id}', 'Api\Transaksi_Controller@show');
    Route::get('transaksi_store/{id}', 'Api\Transaksi_Controller@store');
    Route::put('transaksi_update/{id}', 'Api\Transaksi_Controller@update');

    Route::get('kartu', 'Api\Kartu_Controller@index');
    Route::get('kartu_tipe/{tipe}', 'Api\Kartu_Controller@indexTipe');
    Route::get('kartu/{id}', 'Api\Transaksi_Controller@show');
    Route::post('kartu', 'Api\Kartu_Controller@store');
    Route::get('kartu_cek/{no}', 'Api\Kartu_Controller@cekKartu');

    Route::get('pesanan', 'Api\Pesanan_Controller@index');
    Route::get('pesanan/{id}', 'Api\Pesanan_Controller@show');
    Route::post('pesanan', 'Api\Pesanan_Controller@store');
    Route::put('pesanan/{id}', 'Api\Pesanan_Controller@update');
    Route::get('pesanan_transaksi/{id}', 'Api\Pesanan_Controller@indexOneTransaksi');
    Route::get('pesanan_subtotal/{id}', 'Api\Pesanan_Controller@sumSubTotal');

    Route::get('bahan', 'Api\Bahan_Controller@index');
    Route::get('bahan/{id}', 'Api\Bahan_Controller@show');
    Route::post('bahan', 'Api\Bahan_Controller@store');
    Route::put('bahan/{id}', 'Api\Bahan_Controller@update');
    Route::get('bahan_hapus/{id}', 'Api\Bahan_Controller@destroy');

    Route::get('menu', 'Api\Menu_Controller@index');
    Route::get('menu/{id}', 'Api\Menu_Controller@show');
    Route::post('menu', 'Api\Menu_Controller@store');
    Route::put('menu/{id}', 'Api\Menu_Controller@update');
    Route::get('menu_hapus/{id}', 'Api\Menu_Controller@destroy');

    Route::get('histori_masuk', 'Api\Histori_Masuk_Controller@index');
    Route::get('histori_masuk/{id}', 'Api\Histori_Masuk_Controller@show');
    Route::post('histori_masuk', 'Api\Histori_Masuk_Controller@store');
    Route::put('histori_masuk/{id}', 'Api\Histori_Masuk_Controller@update');
    Route::get('histori_masuk_hapus/{id}', 'Api\Histori_Masuk_Controller@destroy');

    Route::get('histori_keluar', 'Api\Histori_Keluar_Controller@index');
    Route::get('histori_keluar/{id}', 'Api\Histori_Keluar_Controller@show');
    Route::get('histori_keluar_store/{id}', 'Api\Histori_Keluar_Controller@store');
    Route::post('histori_keluar', 'Api\Histori_Keluar_Controller@tambah');
    Route::get('histori_terbuang', 'Api\Histori_Keluar_Controller@waste');

    Route::get('laporan_pengeluaran_bulanan/{year}', 'Api\Histori_Masuk_Controller@laporanPengeluaranBln');
    Route::get('laporan_pengeluaran_tahunan/{yearF}_{yearL}', 'Api\Histori_Masuk_Controller@laporanPengeluaranThn');
    Route::get('laporan_pendapatan_bulanan/{year}', 'Api\Transaksi_Controller@laporanPendapatanBln');
    Route::get('laporan_pendapatan_tahunan/{yearF}_{yearL}', 'Api\Transaksi_Controller@laporanPendapatanThn');
    Route::get('laporan_penjualan_one/{dt}', 'Api\Menu_Controller@laporanPenjualanOne');
    Route::get('laporan_penjualan_all/{dt}', 'Api\Menu_Controller@laporanPenjualanAll');
    Route::get('laporan_bahan_custom/{yearF}_{yearL}', 'Api\Bahan_Controller@laporanBahanCustom');

    Route::get('logout','Api\Karyawan_Controller@logout');
});

Route::get('menu_mobile', 'Api\Menu_Controller@index');
Route::get('menu_mobile/{id}', 'Api\Menu_Controller@show');

// Route::get('pesanan_mobile/{id}', 'Api\Pesanan_Controller@show');
Route::post('pesanan_mobile', 'Api\Pesanan_Controller@store');
Route::get('pesanan_mobile/{id}', 'Api\Pesanan_Controller@indexOneTransaksi');
Route::get('pesanan_subtotal_mobile/{id}', 'Api\Pesanan_Controller@sumSubTotal');

Route::get('transaksi_mobile/{id}', 'Api\Transaksi_Controller@show');

Route::get('reservasi_scan/{kode}', 'Api\Reservasi_Controller@scanQRCode');

Route::get('reservasi_qrcode/{id}', 'Api\Reservasi_Controller@generateQRCode');

// Route::get('transaksi_struk', 'Api\Transaksi_Controller@struk');

// Route::get('reservasi_pdf/{id}', 'Api\Reservasi_Controller@pdf');