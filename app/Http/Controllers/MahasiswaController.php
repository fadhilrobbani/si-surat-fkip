<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\User;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function dashboard(){
        return view('mahasiswa.dashboard');
    }

    public function pengajuanSurat(){
        return view('mahasiswa.pengajuan-surat',[
            'daftarJenisSurat' => JenisSurat::all(),
        ]);
    }

    public function riwayatPengajuanSurat(){
        return view('mahasiswa.riwayat-pengajuan');
    }

    public function lacakSurat(){
        return view('mahasiswa.lacak-surat');
    }

    public function index(){
        return view('admin.users.mahasiswa.index',[
            'daftarMahasiswa' => User::where('role_id', 2)->get()
        ]);
    }

    public function create()
    {
        return view('admin.users.mahasiswa.create');
    }

    public function edit(){
        return view('admin.users.mahasiswa.edit');
    }
    public function store(Request $request)
    {
        //
    }
}
