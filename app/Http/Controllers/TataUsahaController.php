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

class TataUsahaController extends Controller
{
    public function dashboard()
    {
        return view('tata-usaha.dashboard', [
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
            'suratMasuk' => Surat::where('current_user_id', auth()->user()->id)->where('status', 'diproses')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })->count(),
            'suratDisetujui' => Approval::where('user_id', auth()->user()->id)->where('isApproved', 1)->count(),
            'suratDitolak' => Approval::where('user_id', auth()->user()->id)->where('isApproved', 0)->count(),
        ]);
    }

    public function index()
    {
        return view('admin.users.tata-usaha.index');
    }

    public function profilePage()
    {
        return view('tata-usaha.profile', [
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
            Storage::disk(config('filesystems.default'))->put('stempel/' . $uuid, file_get_contents($file));
            $user->update(['tandatangan' => 'stempel/' . $uuid]);
        }
        $user->update($request->only('name'));
        return redirect('/tata-usaha/profile')->with('success', 'Sukses mengupdate data');
    }

    public function suratMasuk(Request $request)
    {
        $query = Surat::with(['pengaju', 'pengaju.programStudi', 'jenisSurat'])
            ->where('current_user_id', auth()->user()->id)
            ->where('status', 'diproses')
            ->where(function ($q) {
                $now = Carbon::now();
                $q->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            });

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('pengaju', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('username', 'LIKE', '%' . $search . '%');
                })->orWhere('data', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('jenis-surat')) {
            $query->where('jenis_surat_id', $request->get('jenis-surat'));
        }

        $order = $request->get('order') == 'asc' ? 'asc' : 'desc';
        $daftarSuratMasuk = $query->orderBy('created_at', $order)
            ->paginate(10)
            ->appends(request()->query());

        return view('tata-usaha.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::all(),
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        return view('tata-usaha.show-surat', [
            'surat' => $surat
        ]);
    }

    public function setujuiSurat(Request $request, Surat $surat)
    {
        if (in_array($surat->jenisSurat->slug, ['surat-peminjaman-ruang', 'surat-peminjaman-ruang-mahasiswa'])) {
            if ($request->filled('no-surat')) {
                $request->validate([
                    'no-surat' => [
                        'required',
                        'string',
                        'max:100',
                        function ($attribute, $value, $fail) {
                            if (!str_contains($value, '/')) {
                                $fail('Nomor surat harus berformat lengkap dengan kode instansi (contoh: 042/DST/UN30.7.11/PP/' . date('Y') . '). Gunakan tombol bantuan di bawah kolom.');
                            }
                        },
                    ],
                ]);
            }

            $surat->current_user_id = $surat->pengaju_id;
            $surat->status = 'selesai';
            $surat->expired_at = null;
            $data = $surat->data;
            $data['tanggal_selesai'] = formatTimestampToOnlyDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
            $data['catatanTU'] = $request->input('catatan') ?? $request->input('note');
            if ($request->filled('no-surat')) {
                $data['noSurat'] = $request->input('no-surat');
            }
            if (isset($data['private']['stepper'])) {
                $data['private']['stepper'][] = auth()->user()->role->id;
            }
            $surat->data = $data;
            $surat->save();

            Approval::create([
                'surat_id' => $surat->id,
                'user_id' => auth()->user()->id,
                'isApproved' => true,
                'note' => $data['catatanTU'] ?? 'Disetujui Tata Usaha'
            ]);

            return redirect('/tata-usaha/surat-masuk')->with('success', 'Surat peminjaman ruang berhasil disetujui');
        }

        $surat->current_user_id = $surat->jenisSurat->user_type == 'staff' || $surat->jenisSurat->user_type == 'akademik' || $surat->jenisSurat->user_type == 'akademik_fakultas' || $surat->jenisSurat->user_type == 'kemahasiswaan' || $surat->jenisSurat->user_type == 'tata-usaha' ? User::select('id', 'name', 'username')->where('role_id', '=', 17)->first()->id : null;
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
        return redirect('/tata-usaha/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function confirmTolakSurat(Surat $surat)
    {
        return view('tata-usaha.confirm-tolak-surat', [
            'surat' => $surat
        ]);
    }

    public function tolakSurat(Request $request, Surat $surat)
    {
        $surat->current_user_id = null;
        $surat->status = 'ditolak';
        $data = $surat->data;
        $alasan = $request->input('catatan') ?? $request->input('note') ?? 'Pengajuan ditolak oleh Tata Usaha';
        $data['alasanPenolakan'] = $alasan;
        if (isset($data['private']['stepper'])) {
            $data['private']['stepper'][] = auth()->user()->role->id;
        }
        $surat->data = $data;
        $surat->save();

        Approval::create([
            'surat_id' => $surat->id,
            'user_id' => auth()->user()->id,
            'isApproved' => false,
            'note' => $alasan
        ]);
        return redirect('/tata-usaha/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }

    public function riwayatPersetujuan(Request $request)
    {
        $query = Approval::with(['surat', 'surat.pengaju', 'surat.pengaju.programStudi', 'surat.jenisSurat'])
            ->where('user_id', auth()->user()->id);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('surat', function ($sq) use ($search) {
                $sq->whereHas('pengaju', function ($uq) use ($search) {
                    $uq->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('username', 'LIKE', '%' . $search . '%');
                })->orWhere('data', 'LIKE', '%' . $search . '%');
            });
        }

        $order = $request->get('order') == 'asc' ? 'asc' : 'desc';
        $daftarRiwayatPersetujuan = $query->orderBy('created_at', $order)
            ->paginate(10)
            ->appends(request()->query());

        return view('tata-usaha.riwayat-persetujuan', [
            'daftarRiwayatPersetujuan' => $daftarRiwayatPersetujuan
        ]);
    }

    public function showApproval(Approval $approval)
    {
        $surat = $approval->surat;
        return view('tata-usaha.show-approval', [
            'surat' => $surat,
            'approval' => $approval
        ]);
    }

    public function pengajuanSurat()
    {
        $daftarJenisSurat = JenisSurat::where('user_type', 'tata-usaha')->get();
        return view('tata-usaha.pengajuan-surat', [
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

        return view('tata-usaha.riwayat-pengajuan', [
            'daftarPengajuan' => $daftarPengajuan,
            'daftarJenisSurat' => JenisSurat::where('user_type', '=', 'tata-usaha')->get(),
            'daftarStatus' => ['diproses', 'ditolak', 'selesai'],
        ]);
    }

    public function showDetailPengajuanSuratByTataUsaha(Surat $surat)
    {
        return view('tata-usaha.show-surat', [
            'surat' => $surat,
        ]);
    }

    public function resetPasswordPage()
    {
        return view('tata-usaha.reset-password');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/tata-usaha/profile')->with('success', 'Password berhasil direset');
    }
}