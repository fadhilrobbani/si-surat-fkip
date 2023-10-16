<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function dashboard(){
        return view('mahasiswa.dashboard');
    }

    public function index(){
        return view('admin.users.mahasiswa.index');
    }
}
