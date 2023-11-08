<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        return view('mahasiswa.dashboard',[
            'pengajuanSelesai' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status','=','finished')->get(),
            'pengajuanDitolak' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status','=','denied')->get(),
            'pengajuanDiproses' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status','=','on_process')->get(),
            'pengajuanKadaluarsa' =>  Surat::where('pengaju_id', '=', auth()->user()->id)->where('status','=','on_process')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '<', $now);})->get(),
        ]);
    }

    public function profilePage(){
        return view('mahasiswa.profile',[
            'daftarProgramStudi' => ProgramStudi::all()
        ]);
    }

    public function updateProfile(Request $request, User $user){
        $request->validate([
            'username' => 'string|required',
            'name' => 'string|required',
            'email' =>'email|required',
            'program-studi' => 'required'
        ]);

        if($request->input('username') != $user->username){
            $request->validate([
                'username' => 'unique:users,username'
            ]);
            $user->update($request->only('username'));
        }

        if($request->input('email') != $user->email){
            $request->validate([
                'email' => 'unique:users,email'
            ]);
            $user->update($request->only('email'));
            $user->email_verified_at = null;
        }
        $user->update($request->only('name','program-studi'));
        return redirect('/mahasiswa/profile')->with('success','Sukses mengupdate data');
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
            'daftarPengajuan' => Surat::where('pengaju_id', '=', auth()->user()->id)->latest()->paginate(10),

        ]);
    }

    public function lihatSurat(Surat $surat)
    {
        return view('mahasiswa.show-surat', [
            'surat' => $surat
        ]);
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
