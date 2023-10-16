<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaprodiController extends Controller
{
    public function dashboard(){
        return view('kaprodi.dashboard');
    }

    public function index(){
        return view('admin.users.kaprodi.index');
    }
}
