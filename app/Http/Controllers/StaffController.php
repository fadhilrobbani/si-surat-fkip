<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\User;
use App\Models\Surat;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function dashboard()
    {
        return view('staff.dashboard');
    }

    public function suratMasuk()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->get();
        return view('staff.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function suratDisetujui()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->get();
        return view('staff.surat-disetujui', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function setujuiSurat(Surat $surat)
    {
        $wd1 = User::select('id')
            ->where('role_id', '=', 5)
            ->first();

        $surat->current_user_id = $surat->penerima_id;
        $surat->penerima_id = $wd1->id;
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => 'setuju',
        ]);
        return redirect()->back()->with('success', 'Surat berhasil disetujui');
    }

    public function index()
    {
        return view('admin.users.staff.index');
    }
}
