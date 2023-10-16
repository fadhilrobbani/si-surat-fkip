<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WDController extends Controller
{
    public function dashboard(){
        return view('wd.dashboard');
    }

    public function index(){
        return view('admin.users.wd.index');
    }
}
