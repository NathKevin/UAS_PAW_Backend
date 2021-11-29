<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; //use tambahan
use Validator; // import library validasi
use App\Models\Karyawan; //import model Karyawan

class KaryawanController extends Controller
{
    //method untnuk menampilkan semua data Karyawan (read)
    public function index(){
        $karyawan = Karyawan::all(); // mengambil semua data 

        if(count($karyawan)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $karyawan
            ], 200);
        } //return data semua dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); //return message data kosong
    }

    //method untuk menampilkan 1 data karyawan (search)
    public function show($id){
        $karyawan = Karyawan::find($id); // mencari data berdasarkan id

        if(!is_null($karyawan)){
            return response([
                'message' => 'Retrieve Karyawan Success',
                'data' => $karyawan
            ], 200);
        } //return data yang ditemukan dalam bentuk json

        return response([
            'message' => 'Karyawan Not Found',
            'data' => null
        ], 400); //return message data tidak ditemukan
    }

    //method untuk menambah 1 data karyawan baru (create)
    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'name' => 'required',
            'noTelp' => 'required|numeric|regex:/^((08))/|digits_between:10,13',
            'alamat' => 'required',
            'cabang' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date_format:Y-m-d'
        ]); //membuat rule validasi input data

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $karyawan = Karyawan::create($storeData);
        return response([
            'message' => 'Add Karyawan Success',
            'data' => $karyawan
        ], 200); //return data baru dalam bentuk json
    }

    //method untuk menghapus 1 data Karyawan (delete)
    public function destroy($id){
        $karyawan = Karyawan::find($id); //mencari data berdasarkan id

        if(is_null($karyawan)){
            return response([
                'message' => 'Karyawan Not Found',
                'data' => null
            ], 404); 
        }// return message saat data tidak ditemukan

        if($karyawan->delete()){
            return response([
                'message' => 'Delete Karyawan Success',
                'data' => $karyawan
            ], 200);
        }//return message saat berhasil hapus data 

        return response([
            'message' => 'Delete Karyawan Failed',
            'data' => null
        ], 400); //return message saat gagal hapus data 
    }

    //method untuk mengubah 1 data karyawan (update)
    public function update(Request $request, $id){
        $karyawan = Karyawan::find($id); // mencari data berdasarkan id
        if(is_null($karyawan)){
            return response([
                'message' => 'Karyawan Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'name' => 'required',
            'noTelp' => 'required|numeric|regex:/^((08))/|digits_between:10,13',
            'alamat' => 'required',
            'cabang' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date_format:Y-m-d'
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //mengedit timpa data yang lama dengan data yang baru
        $karyawan->name = $updateData['name'];
        $karyawan->noTelp = $updateData['noTelp'];
        $karyawan->alamat = $updateData['alamat'];
        $karyawan->cabang = $updateData['cabang'];
        $karyawan->tempat_lahir = $updateData['tempat_lahir'];
        $karyawan->tanggal_lahir = $updateData['tanggal_lahir'];

        if($karyawan->save()){
            return response([
                'message' => 'Update Karyawan Success',
                'data' => $karyawan
            ], 200);
        }// return data yang telah di edit dalam bentuk json

        return response([
            'message' => 'Update Karyawan Failed',
            'data' => null
        ], 400); //return message saat data gagal di edit
    }
}