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
    // Route::get('histori_masuk_hapus/{id}', 'Api\Histori_Masuk_Controller@destroy');

    Route::get('logout','Api\Karyawan_Controller@logout');
});
