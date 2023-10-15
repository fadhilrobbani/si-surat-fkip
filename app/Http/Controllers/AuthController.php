<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login() {
        return view('auth.login');
    }

    public function register(){
        return view('auth.register');
    }

    public function logout(){
        auth()->logout();
        return redirect('/');
    }
    public function authenticate(Request $request){
        $user = User::where('username', $request->input('username'))->first();
        if(!$user){
            return back()->withErrors(['username' => 'Username/NPM salah atau tidak terdaftar'])->withInput();
        }
        $credentials = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required'
        ]);

        if(auth()->attempt($credentials)){
          $request->session()->regenerate();
          if(auth()->user()->role_id == 1){
              return redirect('/admin')->with('success', 'Anda berhasil login');
          }elseif(auth()->user()->role_id == 2){
            return redirect('/mahasiswa')->with('success', 'Anda berhasil login');
          }elseif(auth()->user()->role_id == 3){
            return redirect('/staff')->with('success', 'Anda berhasil login');
          }elseif(auth()->user()->role_id == 4){
            return redirect('/kaprodi')->with('success', 'Anda berhasil login');
          }elseif(auth()->user()->role_id == 5){
            return redirect('/wd')->with('success', 'Anda berhasil login');
          }
        }

        return back()->withErrors('Gagal Login')->withInput();
}


}
