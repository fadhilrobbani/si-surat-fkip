<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KaprodiController extends Controller
{
    public function dashboard()
    {
        return view('kaprodi.dashboard');
    }

    public function index()
    {
        return view('admin.users.kaprodi.index');
    }

    public function suratMasuk()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })->get();
        return view('kaprodi.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id == auth()->user()->id) {

            return view('kaprodi.show-surat', [
                'surat' => $surat
            ]);
        }
        return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('kaprodi.show-approval', [
            'approval' => $approval
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function riwayatPersetujuan()
    {
        $daftarRiwayatSurat = Approval::where('user_id', '=', auth()->user()->id)->latest()->get();
        return view('kaprodi.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat
        ]);
    }

    public function setujuiSurat(Surat $surat)
    {
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
        $idJurusan = User::join('program_studi_tables as pst', 'users.program_studi_id', '=', 'pst.id')
            ->join('jurusan_tables as jt', 'pst.jurusan_id', '=', 'jt.id')
            ->where('users.id', $surat->pengaju->id)
            ->select('jt.id')
            ->first();
        $akademik = User::select('id')
            ->where('role_id', '=', 6)
            ->where('jurusan_id', '=', $idJurusan->id)
            ->first();

        $surat->current_user_id = $surat->penerima_id;
        $surat->penerima_id = $akademik->id;
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => 'setuju',
        ]);
        return redirect('kaprodi/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }


    public function confirmTolakSurat(Surat $surat)
    {
        return view('kaprodi.confirm-tolak', [
            'surat' => $surat
        ]);
    }

    public function tolakSurat(Request $request, Surat $surat)
    {
        $surat->status = 'denied';
        $surat->expired_at = null;
        $surat->penerima_id = null;
        $data = $surat->data;
        $data['alasanPenolakan'] = $request->input('note');
        $surat->data = $data;
        $surat->save();
        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => false,
            'note' => $request->input('note'),
        ]);
        return redirect('/kaprodi/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }
}
