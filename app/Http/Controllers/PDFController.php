<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

class PDFController extends Controller
{

    public function printSurat(Surat $surat)
    {
        if ($surat->status != 'finished') {
            return redirect()->back()->withErrors('deleted', 'Anda tidak bisa mengakses fungsi ini');
        }
        if ($surat->jenisSurat->slug == 'surat-keterangan-alumni') {
            $pdf = Pdf::loadview('template.surat-keterangan-alumni', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-keterangan-lulus') {
            $pdf = Pdf::loadview('template.surat-keterangan-lulus', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }
        return $pdf->stream();
    }


    public function liveTest(Surat $surat)
    {
        return view('template.surat-keterangan-alumni', ['surat' => $surat]);
    }

    public function previewSurat(Surat $surat)
    {
        if ($surat->jenisSurat->id == 6) {
            $pdf = Pdf::loadview('template.surat-keterangan-alumni', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }
        if ($surat->jenisSurat->id == 8) {
            $pdf = Pdf::loadview('template.surat-keterangan-lulus', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }
        return $pdf->stream();
    }

    public function sendSuratKeteranganAlumni(Surat $surat)
    {
        $pdf = Pdf::loadview('template.surat-keterangan-alumni', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
            'tempDir' => public_path(),
            'chroot' => public_path()
        ]);
    }
}
