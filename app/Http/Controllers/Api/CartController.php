<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; //use tambahan
use Validator; // import library validasi
use App\Models\Cart; //import model Cart
use App\Models\User; //import model User
use App\Models\Produk; //import model Produk

class CartController extends Controller
{
    //method untnuk menampilkan semua data Cart (read)
    //ini ga kepake di vue nya nanti, cuma buat cek aja di postman
    public function index(){
        $cart = Cart::all(); // mengambil semua data 

        if(count($cart)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $cart
            ], 200);
        } //return data semua dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); //return message data kosong
    }

    //method untuk menampilkan data cart sesuai id user dan jika checkout nya masih 0 (search)
    //ini untuk panggil semua data cart milik user terentu yang belum di checkout cart nya
    public function show($id1, $check){
        $cart = Cart::where('user_id', $id1)->where('checkout', $check)->get(); // mencari data berdasarkan id

        if(count($cart)>0){
            return response([
                'message' => 'Retrieve Cart Success',
                'data' => $cart
            ], 200);
        } //return data yang ditemukan dalam bentuk json

        return response([
            'message' => 'Cart Not Found',
            'data' => null
        ], 400); //return message data tidak ditemukan
    }

    //method untuk mencari data cart sesuai id user dan jika checkout nya masih 0 (search)
    //ini untuk panggil semua data cart milik user terentu yang belum di checkout cart nya
    public function search($id1, $id2, $check){
        $cart = Cart::where('user_id', $id1)->where('produk_id', $id2)->where('checkout', $check)->first(); // mencari data berdasarkan id

        if(!is_null($cart)){
            return response([
                'message' => 'Retrieve Cart Success',
                'data' => $cart
            ], 200);
        } //return data yang ditemukan dalam bentuk json

        return response([
            'message' => 'Cart Not Found',
            'data' => null
        ],); //return message data tidak ditemukan
            // sengaja tidak ada error handling agar return data null dapat dipakai suatu kondisi
    }

    //method untuk menambah 1 data cart baru (create)
    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'user_id' => 'required|numeric',
            'produk_id' => 'required|numeric',
            'qty' => 'required|numeric',
            'harga_total' => 'required|numeric',
            'checkout' => 'required|numeric'
        ]); //membuat rule validasi input data

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $cart = Cart::create($storeData);
        return response([
            'message' => 'Add Cart Success',
            'data' => $cart
        ], 200); //return data baru dalam bentuk json
    }

    //method untuk mengubah 1 data cart pada atribut qty dan total harga saja (update)
    //ini untuk edit harga total sama qty aja
    public function update(Request $request, $id1, $id2, $check){
        $cart = Cart::where('user_id', $id1)->where('produk_id', $id2)->where('checkout', $check)->first(); // mencari data berdasarkan id
        if(is_null($cart)){
            return response([
                'message' => 'Cart Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'qty' => 'required|numeric',
            'harga_total' => 'required|numeric',
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //mengedit timpa data yang lama dengan data yang baru
        $cart->qty = $updateData['qty'];
        $cart->harga_total = $updateData['harga_total'];

        if($cart->save()){
            return response([
                'message' => 'Success',
                'data' => $cart
            ], 200);
        }// return data yang telah di edit dalam bentuk json

        return response([
            'message' => 'Update Cart Failed',
            'data' => null
        ], 400); //return message saat data gagal di edit
    }

    //method untuk mengubah 1 data cart pada checkout menjadi 1 (update)
    //ini untuk checkout, jadi ketika user mencet tombol checkout, maka atribut checkout
    //di database jadi 1. datanya ga di delete
    public function checkout(Request $request, $id1, $check){
        $cart = Cart::where('user_id', $id1)->where('checkout', $check)->get(); // mencari data berdasarkan id
        if(is_null($cart)){
            return response([
                'message' => 'Cart Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'checkout' => 'required|numeric'
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //mengedit timpa data yang lama dengan data yang baru
        foreach($cart as $result){
            $result->checkout = $updateData['checkout'];
            $result->save();
        }
        

        if(!is_null($cart)){
            return response([
                'message' => 'Checkout Payment Success',
                'data' => $cart
            ], 200);
        }// return data yang telah di edit dalam bentuk json

        return response([
            'message' => 'Update Cart Failed',
            'data' => null
        ], 400); //return message saat data gagal di edit
    }

    //method untuk menghapus 1 data produk (delete)
    public function destroy($id){
        $cart = Cart::find($id); //mencari data berdasarkan id

        if(is_null($cart)){
            return response([
                'message' => 'Produk Not Found',
                'data' => null
            ], 404); 
        }// return message saat data tidak ditemukan

        if($cart->delete()){
            return response([
                'message' => 'Delete Produk Success',
                'data' => $cart
            ], 200);
        }//return message saat berhasil hapus data 

        return response([
            'message' => 'Delete Produk Failed',
            'data' => null
        ], 400); //return message saat gagal hapus data 
    }
}