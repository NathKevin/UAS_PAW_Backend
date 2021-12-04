<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; //use tambahan
use App\Models\User; //import model user
use Validator; // import library validasi

class AuthController extends Controller
{
    public function register(Request $request){
        $registrationData = $request->all();
        $validate = Validator::make($registrationData,[
            'name' => 'required|max:60',
            'noTelp' => 'required|digits_between:10,13|regex:/^((08))/|numeric',
            'alamat' => 'required',
            'userType' => 'required',
            'verify' => 'required',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required'
        ]); // membuat rule validasi input register

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); // return error validasi input 
        
        $registrationData['password'] = bcrypt($request->password); //enkripsi password
        $user = User::create($registrationData); // membuat user baru
        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200); //return data user dalam bentuk json
    }

    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]); //membuat rule validasi input login

        if($validate->fails())
            return response(['message' => $validate->errors()],400); // return error validasi
        
        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'],401); // return gagal login

        $user = Auth::user();
        if($user->verify == 0)
            return response(['message' => 'Verify Account First'],401); // return gagal login

        $token = $user->createToken('Authentication Token')->accessToken; //generate token

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]); // return data user dan token dalam bentuk json
    }

    //method untuk mengubah 1 data User (update)
    //ini untuk edit profile nanti
    public function update(Request $request, $id){
        $user = User::find($id); 
        if(is_null($user)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan
        
        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'name' => 'required|max:60',
            'noTelp' => 'required|digits_between:10,13|regex:/^((08))/|numeric',
            'alamat' => 'required',
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //mengedit timpa data yang lama dengan data yang baru
        $user->name = $updateData['name'];
        $user->noTelp = $updateData['noTelp'];
        $user->alamat = $updateData['alamat'];

        if($user->save()){
            return response([
                'message' => 'Update User Success',
                'data' => $user
            ], 200);
        }// return data course yang telah di edit dalam bentuk json

        return response([
            'message' => 'Update User Failed',
            'data' => null
        ], 400); //return message saat course gagal di edit
    }

    //show user
    public function show($id){
        $user = User::find($id); // mencari data berdasarkan id

        if(!is_null($user)){
            return response([
                'message' => 'Retrieve Produk Success',
                'data' => $user
            ], 200);
        } //return data yang ditemukan dalam bentuk json

        return response([
            'message' => 'Produk Not Found',
            'data' => null
        ], 400); //return message data tidak ditemukan
    }

}