<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AkademikController extends Controller
{
    public function dashboard()
    {
        return view('akademik.dashboard');
    }

    public function index()
    {
        return view('admin.users.akademik.index');
    }

    public function suratMasuk()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })->get();
        return view('akademik.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function suratDisetujui()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->get();
        return view('akademik.surat-disetujui', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function setujuiSurat(Surat $surat)
    {
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
        $surat->current_user_id = $surat->pengaju_id;
        $surat->penerima_id = $surat->pengaju_id;
        $surat->expired_at = null;
        $data = $surat->data;
        $data['tanggal_selesai'] = Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s');
        $surat->data = $data;
        $surat->status = 'finished';
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
        return view('akademik.confirm-tolak', [
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
        return redirect('/akademik/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }
}
