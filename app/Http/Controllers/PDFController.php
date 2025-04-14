<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\Surat;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

class PDFController extends Controller
{

    public function previewSurat(Surat $surat)
    {
        if ($surat->jenisSurat->slug == 'legalisir-ijazah' || $surat->jenisSurat->slug == 'legalisir-transkrip') {
            $pdf = Pdf::loadview('template.invoice-legalisir', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
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

        if ($surat->jenisSurat->slug == 'surat-permohonan-izin-penelitian-mahasiswa') {
            $pdf = Pdf::loadview('template.surat-permohonan-izin-penelitian-mahasiswa', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }
        if ($surat->jenisSurat->slug == 'surat-permohonan-izin-prapenelitian-mahasiswa') {
            $pdf = Pdf::loadview('template.surat-permohonan-izin-prapenelitian-mahasiswa', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-keterangan-kesalahan-ijazah') {
            $pdf = Pdf::loadview('template.surat-keterangan-kesalahan-ijazah', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-rekomendasi-mbkm') {
            $pdf = Pdf::loadview('template.surat-rekomendasi-mbkm', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-pengantar-pembayaran-uang-yudisium') {
            $pdf = Pdf::loadview('template.surat-pengantar-pembayaran-uang-yudisium', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        // STAFF

        if ($surat->jenisSurat->slug == 'surat-tugas') {
            $pdf = Pdf::loadview('template.surat-tugas', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-tugas-kelompok') {
            $pdf = Pdf::loadview('template.surat-tugas-kelompok', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }

        // STAFF DEKAN
        if ($surat->jenisSurat->slug == 'surat-keluar') {
            $pdf = Pdf::loadview('template.surat-keluar', ['surat' => $surat])->setPaper('a4', 'potrait')->setOptions([
                'tempDir' => public_path(),
                'chroot' => public_path()
            ]);
        }
        return $pdf->stream();
    }

    public function printSurat(Surat $surat)
    {
        // if ($surat->status != 'selesai') {
        //     return redirect()->back()->withErrors('deleted', 'Anda tidak bisa mengakses fungsi ini');
        // }
        return $this->previewSurat($surat);
    }

    // public function templateSurat(JenisSurat $jenisSurat)
    // {
    //     if ($jenisSurat->slug == 'surat-keterangan-alumni') {
    //         $pdf = Pdf::loadview('template.surat-keterangan-alumni',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }
    //     if ($jenisSurat->slug == 'surat-keterangan-lulus') {
    //         $pdf = Pdf::loadview('template.surat-keterangan-lulus',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }
    //     if ($jenisSurat->slug == 'surat-keterangan-pernah-kuliah') {
    //         $pdf = Pdf::loadview('template.surat-keterangan-pernah-kuliah',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }

    //     if ($jenisSurat->slug == 'surat-aktif-kuliah') {
    //         $pdf = Pdf::loadview('template.surat-keterangan-aktif-kuliah',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }

    //     if ($jenisSurat->slug == 'surat-keterangan-eligible-pin') {
    //         $pdf = Pdf::loadview('template.surat-keterangan-eligible-pin',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }

    //     if ($jenisSurat->slug == 'surat-permohonan-izin-penelitian-mahasiswa') {
    //         $pdf = Pdf::loadview('template.surat-permohonan-izin-penelitian-mahasiswa',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }

    //     if ($jenisSurat->slug == 'surat-keterangan-kesalahan-ijazah') {
    //         $pdf = Pdf::loadview('template.surat-keterangan-kesalahan-ijazah',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }

    //     if ($jenisSurat->slug == 'surat-rekomendasi-mbkm') {
    //         $pdf = Pdf::loadview('template.surat-rekomendasi-mbkm',['surat' => [

    //         ]])->setPaper('a4', 'potrait')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path()
    //         ]);
    //     }
    //     return $pdf->stream();
    // }
}
