<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function authenticate(Request $request){
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(auth()->attempt($credentials)){
          $request->session()->regenerate();
            return redirect('/')->with('success', 'Anda berhasil login');
        }

        return back()->withErrors('Username dan password yang dimasukkan tidak sesuai')->withInput();
}


}
