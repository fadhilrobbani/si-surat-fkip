<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

class PDFController extends Controller
{

    public function previewSurat(Surat $surat)
    {
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
        if ($surat->jenisSurat->slug == 'surat-keterangan-pernah-kuliah') {
            $pdf = Pdf::loadview('template.surat-keterangan-pernah-kuliah', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-aktif-kuliah') {
            $pdf = Pdf::loadview('template.surat-keterangan-aktif-kuliah', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-keterangan-eligible-pin') {
            $pdf = Pdf::loadview('template.surat-keterangan-eligible-pin', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }
        return $pdf->stream();
    }

    public function printSurat(Surat $surat)
    {
        if ($surat->status != 'selesai') {
            return redirect()->back()->withErrors('deleted', 'Anda tidak bisa mengakses fungsi ini');
        }
        return $this->previewSurat($surat);
    }
}
