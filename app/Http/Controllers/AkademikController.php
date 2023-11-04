<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SuratMahasiswa;
use App\Models\Surat;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AkademikController extends Controller
{
    public function dashboard()
    {
        return view('akademik.dashboard',[
            'suratDisetujui' => count(Approval::where('user_id', '=', auth()->user()->id)->where('isApproved','=',true)->get()) ,
            'suratDitolak' => count( Approval::where('user_id', '=', auth()->user()->id)->where('isApproved','=',false)->get()),
            'suratMenunggu' => count(Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })->get()->toArray())
        ]);
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
        })->paginate(10);
        return view('akademik.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }
    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id == auth()->user()->id) {

            return view('akademik.show-surat', [
                'surat' => $surat
            ]);
        }
        return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
    }


    public function setujuiSurat(Request $request, Surat $surat)
    {
        $request->validate([
            'no-surat' => 'required|size:4'
        ]);
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
        $surat->current_user_id = $surat->pengaju_id;
        $surat->penerima_id = $surat->pengaju_id;
        $surat->expired_at = null;
        $data = $surat->data;
        $data['tanggal_selesai'] = formatTimestampToOnlyDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
        $data['ttdWD1'] = public_path('images/ttd.png');
        $data['noSurat'] = $request->input('no-surat') ?? str_pad($surat->id, 4, '0', STR_PAD_LEFT);
        $data['note'] = $request->input('note');
        $surat->data = $data;
        $surat->status = 'finished';
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => $request->input('note'),
        ]);
        // Mail::to($surat->pengaju->email)->send(new SuratMahasiswa($surat));
        return redirect('/akademik/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function riwayatPersetujuan()
    {
        $daftarRiwayatSurat = Approval::where('user_id', '=', auth()->user()->id)->latest()->paginate(10);
        return view('akademik.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat
        ]);
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('akademik.show-approval', [
            'approval' => $approval
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
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
        return redirect('/akademik/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }
}
