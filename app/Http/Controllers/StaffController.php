<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StaffController extends Controller
{
    public function dashboard()
    {
        return view('staff.dashboard');
    }

    public function suratMasuk()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })->get();
        return view('staff.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('staff.show-surat', [
            'surat' => $surat
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function riwayatPersetujuan()
    {
        $daftarRiwayatSurat = Approval::where('user_id', '=', auth()->user()->id)->latest()->get();
        return view('staff.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat
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
        return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function index()
    {
        return view('admin.users.staff.index');
    }


    public function confirmTolakSurat(Surat $surat)
    {
        return view('staff.confirm-tolak', [
            'surat' => $surat
        ]);
    }

    public function tolakSurat(Request $request, Surat $surat)
    {
        $surat->status = 'denied';
        $surat->expired_at = null;
        $surat->penerima_id = null;
        $surat->save();
        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => false,
            'note' => $request->input('note'),
        ]);
        return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }
}
