<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function redirectToFormSurat(Request $request){
        $inputValue = $request->input('jenisSurat');
            return redirect('/mahasiswa/pengajuan-surat/'. $inputValue);
    }
}
