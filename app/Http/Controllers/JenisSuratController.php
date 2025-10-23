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

        // Debug: uncomment untuk lihat value
        // dd('Role name: ' . auth()->user()->role->name, 'User type: ' . $jenisSurat->user_type, 'Input: ' . $inputValue);

        // Gunakan role->name untuk consistency dengan existing logic
        return redirect('/' . auth()->user()->role->name . '/' . 'pengajuan-surat/' . $inputValue);
        // return redirect('/' . $jenisSurat->user_type . '/' . 'pengajuan-surat/' . $inputValue);
    }
}
