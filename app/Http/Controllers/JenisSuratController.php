<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function redirectToFormSurat(Request $request)
    {
        $jenisSurat = JenisSurat::where('slug', $request->input('jenisSurat'))->first();
        $inputValue = $request->input('jenisSurat');
        return redirect('/' . auth()->user()->role->name . '/' . 'pengajuan-surat/' . $inputValue);
        // return redirect('/' . $jenisSurat->user_type . '/' . 'pengajuan-surat/' . $inputValue);
    }
}
