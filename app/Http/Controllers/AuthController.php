<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login() {
        return view('user.login');
    }

    public function register(){
        return view('user.register');
    }

    public function logout(){
        auth()->logout();
        return redirect('/');
    }
    public function authenticate(Request $request){
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(auth()->attempt($credentials)){
          $request->session()->regenerate();
            return redirect('/admin')->with('success', 'Anda berhasil login');
        }

        return back()->withErrors('Username dan password yang dimasukkan tidak sesuai')->withInput();
}


}
