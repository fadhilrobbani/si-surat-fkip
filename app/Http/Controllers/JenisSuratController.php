<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function show(JenisSurat $jenisSurat){
        return view('mahasiswa.formsurat.form-keterangan-alumni',[
            'jenisSurat' => $jenisSurat
        ]);
    }

    public function redirectToFormSurat(Request $request){
        $inputValue = $request->input('jenisSurat');
        return redirect('/mahasiswa/pengajuan-surat/'. $inputValue);
    }
}
