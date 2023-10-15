<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WDController extends Controller
{
    public function index(){
        return view('wd.index');
    }
}
