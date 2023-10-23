<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

class PDFController extends Controller
{
    public function printSuratKeteranganAlumni(Surat $surat)
    {
        $pdf = Pdf::loadview('template.surat-keterangan-alumni', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
            'tempDir' => public_path(),
            'chroot' => public_path()
        ]);
        return $pdf->stream();
    }

    public function liveTest(Surat $surat)
    {
        return view('template.surat-keterangan-alumni', ['surat' => $surat]);
    }
}
