<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; //use tambahan
use Validator; // import library validasi
use App\Models\Produk; //import model Produk

class ProdukController extends Controller
{
    //method untnuk menampilkan semua data produk (read)
    public function index(){
        $produk = Produk::all(); // mengambil semua data 

        if(count($produk)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $produk
            ], 200);
        } //return data semua dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); //return message data kosong
    }

    //method untuk menampilkan 1 data produk (search)
    public function show($id){
        $produk = Produk::find($id); // mencari data berdasarkan id

        if(!is_null($produk)){
            return response([
                'message' => 'Retrieve Produk Success',
                'data' => $produk
            ], 200);
        } //return data yang ditemukan dalam bentuk json

        return response([
            'message' => 'Produk Not Found',
            'data' => null
        ], 400); //return message data tidak ditemukan
    }

    //method untuk menambah 1 data produk baru (create)
    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'name' => 'required',
            'harga' => 'required|numeric',
            'berat' => 'required|numeric',
            'gambar' => 'required'
        ]); //membuat rule validasi input data

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $produk = Produk::create($storeData);
        return response([
            'message' => 'Add Produk Success',
            'data' => $produk
        ], 200); //return data baru dalam bentuk json
    }

    //method untuk menghapus 1 data produk (delete)
    public function destroy($id){
        $produk = Produk::find($id); //mencari data berdasarkan id

        if(is_null($produk)){
            return response([
                'message' => 'Produk Not Found',
                'data' => null
            ], 404); 
        }// return message saat data tidak ditemukan

        if($produk->delete()){
            return response([
                'message' => 'Delete Produk Success',
                'data' => $produk
            ], 200);
        }//return message saat berhasil hapus data 

        return response([
            'message' => 'Delete Produk Failed',
            'data' => null
        ], 400); //return message saat gagal hapus data 
    }

    //method untuk mengubah 1 data produk (update)
    public function update(Request $request, $id){
        $produk = Produk::find($id); // mencari data berdasarkan id
        if(is_null($produk)){
            return response([
                'message' => 'Produk Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'name' => 'required',
            'harga' => 'required|numeric',
            'berat' => 'required|numeric',
            'gambar' => 'required'
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //mengedit timpa data yang lama dengan data yang baru
        $produk->name = $updateData['name'];
        $produk->harga = $updateData['harga'];
        $produk->berat = $updateData['berat'];
        $produk->gambar = $updateData['gambar'];

        if($produk->save()){
            return response([
                'message' => 'Update Produk Success',
                'data' => $produk
            ], 200);
        }// return data yang telah di edit dalam bentuk json

        return response([
            'message' => 'Update Produk Failed',
            'data' => null
        ], 400); //return message saat data gagal di edit
    }
}