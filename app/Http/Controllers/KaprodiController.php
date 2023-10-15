<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaprodiController extends Controller
{
    public function index(){
        return view('kaprodi.index');
    }
}
