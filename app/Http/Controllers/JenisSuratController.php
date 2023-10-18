<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function show(JenisSurat $jenisSurat){
        if($jenisSurat->id == 6){
            return view('mahasiswa.formsurat.form-keterangan-alumni',[
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi'=> ProgramStudi::all(),

            ]);
        }
        return abort(404);
    }

    public function redirectToFormSurat(Request $request){
        $inputValue = $request->input('jenisSurat');
            return redirect('/mahasiswa/pengajuan-surat/'. $inputValue);
    }
}
