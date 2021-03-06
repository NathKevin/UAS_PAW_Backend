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

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::get('email/verify/{id}', 'Api\VerificationController@verify')->name('verificationapi.verify');
Route::get('email/resend', 'Api\VerificationController@resend')->name('verificationapi.resend');

Route::group(['mideleware' => 'auth:api'], function () {
    Route::get('produk', 'Api\ProdukController@index');
    Route::get('produk/{id}', 'Api\ProdukController@show');
    Route::post('produk', 'Api\ProdukController@store');
    Route::put('produk/{id}', 'Api\ProdukController@update');
    Route::delete('produk/{id}', 'Api\ProdukController@destroy');
    //---------------------------------------------------------------------
    Route::get('karyawan', 'Api\KaryawanController@index');
    Route::get('karyawan/{id}', 'Api\KaryawanController@show');
    Route::post('karyawan', 'Api\KaryawanController@store');
    Route::put('karyawan/{id}', 'Api\KaryawanController@update');
    Route::delete('karyawan/{id}', 'Api\KaryawanController@destroy');
    //---------------------------------------------------------------------
    Route::get('user_id/{id1}/checkout/{check}', 'Api\CartController@show');
    Route::post('cart', 'Api\CartController@store');
    Route::delete('cart/destroy/{id}', 'Api\CartController@destroy');
    Route::put('user_id/{id1}/produk_id/{id2}/checkout/{check}', 'Api\CartController@update');
    Route::put('user_id/{id1}/checkout/{check}', 'Api\CartController@checkout');
    Route::get('user_id/{id1}/produk_id/{id2}/checkout/{check}', 'Api\CartController@search');
    //---------------------------------------------------------------------
    Route::put('user/{id}', 'Api\AuthController@update');
    Route::get('user/{id}', 'Api\AuthController@show');
});