<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use Illuminate\Http\Request;

class JenisLegalisirController extends Controller
{
    public function redirectToFormLegalisir(Request $request)
    {
        $jenisSurat = JenisSurat::where('slug', $request->input('jenisSurat'))->first();
        $inputValue = $request->input('jenisSurat');
        // return redirect('/' . auth()->user()->role->name . '/' . 'pengajuan-surat/' . $inputValue);
        return redirect('/' . $jenisSurat->user_type . '/' . 'pengajuan-legalisir/' . $inputValue);
    }
}
