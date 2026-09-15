<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Surat;
use Ramsey\Uuid\Uuid;
use App\Models\Jurusan;
use App\Mail\SuratStaff;
use App\Models\Approval;
use App\Models\JenisSurat;
use App\Mail\SuratMahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BendaharaController extends Controller
{
    public function dashboard()
    {
        return view('bendahara.dashboard', [
            'totalSuratMasuk' => Surat::where('current_user_id', auth()->user()->id)->where('status', 'diproses')->count(),
            'totalDisetujui' => Approval::where('user_id', auth()->user()->id)->where('isApproved', true)->count(),
            'totalDitolak' => Approval::where('user_id', auth()->user()->id)->where('isApproved', false)->count(),
        ]);
    }

    public function profilePage()
    {
        return view('bendahara.profile', [
            'daftarJurusan' => Jurusan::all()
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $request->validate([
            'username' => 'string|required|alpha_dash',
            'name' => 'string|required',
            'email' => 'email|required',
        ]);

        if ($request->input('username') != $user->username) {
            $request->validate([
                'username' => 'unique:users,username'
            ]);
            $user->update($request->only('username'));
        }

        if ($request->input('email') != $user->email) {
            $request->validate([
                'email' => 'unique:users,email'
            ]);
            $user->update($request->only('email'));
            $user->email_verified_at = null;
        }

        if ($request->hasFile('tandatangan')) {
            $request->validate([
                'tandatangan' => 'file|mimes:png|max:2048'
            ]);
            $uuid = Uuid::uuid4();
            $file = $request->file('tandatangan');
            Storage::disk(config('filesystems.default'))->put('ttd/' . $uuid, file_get_contents($file));
            $user->update(['tandatangan' => 'ttd/' . $uuid]);
        }

        $user->update($request->only('name'));
        return redirect('/bendahara/profile')->with('success', 'Sukses mengupdate data profil');
    }

    public function suratMasuk(Request $request)
    {
        $daftarSuratMasuk = Surat::with(['pengaju', 'jenisSurat'])
            ->where('current_user_id', auth()->user()->id)
            ->where('status', 'diproses')
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })
            ->orderBy('created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10)
            ->appends(request()->query());

        if ($request->get('search')) {
            $daftarSuratMasuk = Surat::join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->where('current_user_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where(function ($query) use ($request) {
                    $query->where('users.name', 'LIKE', '%' . $request->get('search') . '%')
                        ->orWhere('users.username', 'LIKE', '%' . $request->get('search') . '%')
                        ->orWhere('jenis_surat_tables.name', 'LIKE', '%' . $request->get('search') . '%');
                })
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        }

        return view('bendahara.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::whereIn('slug', ['surat-pencairan-dana', 'surat-pencairan-dana-mahasiswa'])->get(),
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id != auth()->user()->id) {
            return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
        }

        return view('bendahara.show-surat', [
            'surat' => $surat
        ]);
    }

    public function setujuiSurat(Request $request, Surat $surat)
    {
        $surat->current_user_id = $surat->pengaju_id;
        $surat->expired_at = null;
        $surat->status = 'selesai';

        $data = $surat->data;
        $data['tanggal_selesai'] = formatTimestampToOnlyDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
        $data['catatanBendahara'] = $request->input('note');
        $data['nomorBuktiPencairan'] = $request->input('no_bukti_pencairan');
        $surat->data = $data;
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => $request->input('note') ?? 'Disetujui dan Dicairkan oleh Bendahara',
        ]);

        // Kirim email notifikasi jika ada mailer aktif
        try {
            if ($surat->pengaju->role_id == User::ROLE_MAHASISWA) {
                Mail::to($surat->pengaju->email)->send(new SuratMahasiswa($surat));
            } else {
                Mail::to($surat->pengaju->email)->send(new SuratStaff($surat));
            }
        } catch (\Exception $e) {
            // Abaikan jika mailer offline
        }

        return redirect('/bendahara/surat-masuk')->with('success', 'Pengajuan dana berhasil disetujui dan diselesaikan');
    }

    public function confirmTolakSurat(Surat $surat)
    {
        return view('bendahara.confirm-tolak-surat', [
            'surat' => $surat
        ]);
    }

    public function tolakSurat(Request $request, Surat $surat)
    {
        $surat->status = 'ditolak';
        $surat->expired_at = null;
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

        return redirect('/bendahara/surat-masuk')->with('success', 'Pengajuan dana berhasil ditolak');
    }

    public function riwayatPersetujuan(Request $request)
    {
        $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10)
            ->appends(request()->query());

        return view('bendahara.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat,
            'daftarStatus' => [true => 'Disetujui', false => 'Ditolak'],
        ]);
    }

    public function showApproval(Approval $approval)
    {
        return view('bendahara.show-approval', [
            'approval' => $approval,
            'surat' => $approval->surat
        ]);
    }
}
