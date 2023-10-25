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
}
