<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        return view('mahasiswa.dashboard');
    }

    public function pengajuanSurat()
    {
        return view('mahasiswa.pengajuan-surat', [
            'daftarJenisSurat' => JenisSurat::all(),
        ]);
    }

    public function riwayatPengajuanSurat()
    {
        // dd(auth()->user()->id);
        // $test = Surat::where('pengaju_id', '=', auth()->user()->id)->get();
        // $test2 = Surat::all();
        // dd($test->data);
        return view('mahasiswa.riwayat-pengajuan', [
            'daftarPengajuan' => Surat::where('pengaju_id', '=', auth()->user()->id)->get(),

        ]);
    }

    public function lacakSurat()
    {
        return view('mahasiswa.lacak-surat');
    }

    public function index()
    {
        return view('admin.users.mahasiswa.index', [
            'daftarMahasiswa' => User::where('role_id', 2)->get()
        ]);
    }

    public function create()
    {
        return view('admin.users.mahasiswa.create');
    }

    public function edit()
    {
        return view('admin.users.mahasiswa.edit');
    }
    public function store(Request $request)
    {
        //
    }
}
