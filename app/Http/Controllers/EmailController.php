<?php

namespace App\Http\Controllers;

use App\Mail\SuratMahasiswa;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function send(Surat $surat)
    {
        Mail::to('rabbanifadhillailham@gmail.com')->send(new SuratMahasiswa($surat));
        return '<h1>sukses dikirim</h1>';
    }
    public function reportBug()
    {
        // Logika atau operasi bisnis yang dibutuhkan sebelum redirect

        // Redirect ke Gmail untuk membuat pesan baru
        return redirect()->away('mailto:fadhil@fadhilrobbani.my.id?subject=ESURAT%20FKIP%20BUG%20REPORT');
        // Sesuaikan subject dan body sesuai kebutuhan aplikasi Anda
    }
}
