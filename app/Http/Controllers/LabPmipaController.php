<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Surat;
use Ramsey\Uuid\Uuid;
use App\Models\Jurusan;
use App\Models\Approval;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class LabPmipaController extends Controller
{
    public function dashboard()
    {
        return view('lab-pmipa.dashboard', [
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

    public function profilePage()
    {
        return view('lab-pmipa.profile', [
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
        return redirect('/lab-pmipa/profile')->with('success', 'Sukses mengupdate data');
    }

    public function pengajuanSurat()
    {
        $daftarJenisSurat = JenisSurat::where('user_type', 'lab-pmipa')->get();
        return view('lab-pmipa.pengajuan-surat', [
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

        return view('lab-pmipa.riwayat-pengajuan', [
            'daftarPengajuan' => $daftarPengajuan,
            'daftarJenisSurat' => JenisSurat::where('user_type', '=', 'lab-pmipa')->get(),
            'daftarStatus' => ['diproses', 'ditolak', 'selesai'],
        ]);
    }

    public function showDetailPengajuanSuratByLabPmipa(Surat $surat)
    {
        return view('lab-pmipa.show-surat', [
            'surat' => $surat,
        ]);
    }

    public function resetPasswordPage()
    {
        return view('lab-pmipa.reset-password');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/lab-pmipa/profile')->with('success', 'Password berhasil direset');
    }
}
