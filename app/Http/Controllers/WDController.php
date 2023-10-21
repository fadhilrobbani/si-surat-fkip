<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WDController extends Controller
{
    public function dashboard()
    {
        return view('wd.dashboard');
    }

    public function index()
    {
        return view('admin.users.wd.index');
    }

    public function suratMasuk()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })->get();
        return view('wd.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        return view('wd.show-surat-masuk', [
            'surat' => $surat
        ]);
    }

    public function suratDisetujui()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->get();
        return view('wd.surat-disetujui', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function setujuiSurat(Surat $surat)
    {
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
        // $jurusan = User::select('jurusan_tables.id')
        //     ->join('program_studi_tables as program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
        //     ->join('jurusan_tables as jurusan_tables', 'jurusan_tables.id', '=', 'program_studi_tables.jurusan_id')
        //     ->first();
        // dd($surat->pengaju->id);
        $idJurusan = User::join('program_studi_tables as pst', 'users.program_studi_id', '=', 'pst.id')
            ->join('jurusan_tables as jt', 'pst.jurusan_id', '=', 'jt.id')
            ->where('users.id', $surat->pengaju->id)
            ->select('jt.id')
            ->first();
        $akademik = User::select('id')
            ->where('role_id', '=', 6)
            ->where('jurusan_id', '=', $idJurusan->id)
            ->first();

        //kita buat wd1 bisa memilih penerimanya selain di data misal dari request wd1, tapi sementara manual dulu
        $surat->current_user_id = $akademik->id;
        // $surat->current_user_id = $surat->penerima_id;
        $surat->penerima_id = $surat->pengaju_id;
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => 'setuju',
        ]);
        return redirect()->back()->with('success', 'Surat berhasil disetujui');
    }


    public function confirmTolakSurat(Surat $surat)
    {
        return view('wd.confirm-tolak', [
            'surat' => $surat
        ]);
    }

    public function tolakSurat(Request $request, Surat $surat)
    {
        $surat->status = 'denied';
        $surat->expired_at = null;
        $surat->save();
        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => false,
            'note' => $request->input('note'),
        ]);
        return redirect('/wd/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }
}
