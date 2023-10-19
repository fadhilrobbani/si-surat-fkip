<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function index(){
        return view('admin.surat.index');
    }

    public function create(JenisSurat $jenisSurat){
        if($jenisSurat->id == 6){
            return view('mahasiswa.formsurat.form-keterangan-alumni',[
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi'=> ProgramStudi::all(),

            ]);
        }
        return abort(404);
    }

    public function store(Request $request){
        $surat = $request->validate([
            'pengaju_id' => auth()->user()->id,

            'name' => 'required',
            'username' => 'required',
            'program-studi' => 'required',
            'no-ijazah' => 'required',
            'birthplace' => 'required',
            'birthdate' => 'required|date',
            'email' => 'required|email'
        ]);

        Surat::create($surat);
        return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success','Surat berhasil diajukan');
    }
}
