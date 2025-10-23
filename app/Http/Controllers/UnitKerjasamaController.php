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
use App\Mail\LegalisirDiambil;
use App\Mail\LegalisirDikirim;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Backup\Helpers\Format;

class UnitKerjasamaController extends Controller
{
    public function dashboard()
    {
        return view('unit-kerjasama.dashboard', [
            'pengajuanSelesai' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'selesai')->get(),
            'pengajuanDikirim' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'dikirim')->get(),
            'pengajuanDitolak' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'ditolak')->get(),
            'pengajuanDiproses' =>  Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'diproses')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })->get(),
            'pengajuanMenungguDibayar' =>  Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'menunggu_pembayaran')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })->get(),
            'pengajuanKadaluarsa' =>  Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'diproses')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '<', $now);
            })->get(),
        ]);
    }

    public function index()
    {
        return view('admin.users.unit-kerjasama.index');
    }

    public function profilePage()
    {
        return view('unit-kerjasama.profile', [
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
        if ($request->hasFile('stempel')) {
            $request->validate([
                'stempel' => 'file|mimes:png|max:2048'
            ]);
            $uuid = Uuid::uuid4();
            $file = $request->file('stempel');
            Storage::disk('public')->put('stempel/' . $uuid, file_get_contents($file));
            $user->update(['tandatangan' => 'stempel/' . $uuid]);
        }
        $user->update($request->only('name'));
        return redirect('/unit-kerjasama/profile')->with('success', 'Sukses mengupdate data');
    }

    public function suratMasuk(Request $request)
    {
        $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
            ->where('current_user_id', auth()->user()->id)
            ->where('status', 'diproses')
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('unit-kerjasama.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        return view('unit-kerjasama.show-surat', [
            'surat' => $surat
        ]);
    }

    public function setujuiSurat(Surat $surat)
    {
        $surat->current_user_id = $surat->jenisSurat->user_type == 'staff' || $surat->jenisSurat->user_type == 'akademik' || $surat->jenisSurat->user_type == 'akademik_fakultas' || $surat->jenisSurat->user_type == 'kemahasiswaan' || $surat->jenisSurat->user_type == 'unit-kerjasama' ? User::select('id', 'name', 'username')->where('role_id', '=', 17)->first()->id : null;
        $surat->status = 'diproses';
        $surat->save();

        Approval::create([
            'surat_id' => $surat->id,
            'user_id' => auth()->user()->id,
            'isApproved' => true,
            'catatan' => null
        ]);

        //kirim email ke penerima
        // dd($surat->penerima->email);
        $penerima = User::find($surat->current_user_id);
        $surat->penerima = $penerima;

        if ($surat->pengaju->role->name == 'mahasiswa') {
            Mail::to($surat->penerima->email)->send(new SuratMahasiswa($surat));
        } else {
            Mail::to($surat->penerima->email)->send(new SuratStaff($surat));
        }
        //cek jika user id 17 maka surat dinyatakan selesai
        if ($surat->current_user_id == 17) {
            $surat->status = 'selesai';
            $surat->save();
            //kirim email ke pengaju
            Mail::to($surat->pengaju->email)->send(new SuratMahasiswa($surat));
        }
        return redirect('/unit-kerjasama/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function confirmTolakSurat(Surat $surat)
    {
        return view('unit-kerjasama.confirm-tolak-surat', [
            'surat' => $surat
        ]);
    }

    public function tolakSurat(Request $request, Surat $surat)
    {
        $surat->current_user_id = null;
        $surat->status = 'ditolak';
        $surat->save();

        Approval::create([
            'surat_id' => $surat->id,
            'user_id' => auth()->user()->id,
            'isApproved' => false,
            'catatan' => $request->input('catatan')
        ]);
        return redirect('/unit-kerjasama/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }

    public function riwayatPersetujuan()
    {
        $daftarRiwayatPersetujuan = Approval::with(['surat', 'surat.pengaju', 'surat.pengaju.programStudi'])
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('unit-kerjasama.riwayat-persetujuan', [
            'daftarRiwayatPersetujuan' => $daftarRiwayatPersetujuan
        ]);
    }

    public function showApproval(Approval $approval)
    {
        $surat = $approval->surat;
        return view('unit-kerjasama.show-approval', [
            'surat' => $surat,
            'approval' => $approval
        ]);
    }

    public function pengajuanSurat()
    {
        $daftarJenisSurat = JenisSurat::where('user_type', 'unit-kerjasama')->get();
        return view('unit-kerjasama.pengajuan-surat', [
            'daftarJenisSurat' => $daftarJenisSurat
        ]);
    }

    public function riwayatPengajuanSurat(Request $request)
    {
        $daftarPengajuan = Surat::with('jenisSurat')
            ->where('pengaju_id', '=', auth()->user()->id)
            ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10)
            ->appends(request()->query());

        if ($request->get('search') && $request->get('jenis-surat') && $request->get('status')) {
            $daftarPengajuan = Surat::with('jenisSurat')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->where('surat_tables.pengaju_id', '=',  auth()->user()->id)
                ->where('jenis_surat_tables.name', 'LIKE', '%' . $request->get('search') . '%')
                ->where('surat_tables.status', $request->get('status') == 'expired' ? 'diproses' : $request->get('status'))
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('status') && $request->get('jenis-surat')) {
            $daftarPengajuan = Surat::with('jenisSurat')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->where('surat_tables.pengaju_id', '=',  auth()->user()->id)
                ->where('surat_tables.status', $request->get('status'))
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('status') && $request->get('search')) {
            $daftarPengajuan = Surat::with('jenisSurat')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->where('surat_tables.pengaju_id', '=',  auth()->user()->id)
                ->where('surat_tables.status', $request->get('status'))
                ->where('jenis_surat_tables.name', 'LIKE', '%' . $request->get('search') . '%')
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('jenis-surat') && $request->get('search')) {
            $daftarPengajuan = Surat::with('jenisSurat')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->where('surat_tables.pengaju_id', '=',  auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->where('jenis_surat_tables.name', 'LIKE', '%' . $request->get('search') . '%')
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('status')) {
            $daftarPengajuan = Surat::with('jenisSurat')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->where('surat_tables.pengaju_id', '=',  auth()->user()->id)
                ->where('surat_tables.status', $request->get('status'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('jenis-surat')) {
            $daftarPengajuan = Surat::with('jenisSurat')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->where('surat_tables.pengaju_id', '=',  auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('search')) {
            $daftarPengajuan = Surat::with('jenisSurat')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->where('surat_tables.pengaju_id', '=',  auth()->user()->id)
                ->where('jenis_surat_tables.name', 'LIKE', '%' . $request->get('search') . '%')
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        }

        return view('unit-kerjasama.riwayat-pengajuan', [
            'daftarPengajuan' => $daftarPengajuan,
            'daftarJenisSurat' => JenisSurat::where('user_type', '=', 'unit-kerjasama')->get(),
            'daftarStatus' => ['diproses', 'ditolak', 'selesai'],
        ]);
    }

    public function showDetailPengajuanSuratByUnitKerjasama(Surat $surat)
    {
        return view('unit-kerjasama.show-surat', [
            'surat' => $surat,
        ]);
    }

    public function resetPasswordPage()
    {
        return view('unit-kerjasama.reset-password');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/unit-kerjasama/profile')->with('success', 'Password berhasil direset');
    }
}