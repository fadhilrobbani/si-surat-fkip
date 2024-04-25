<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use Ramsey\Uuid\Uuid;
use App\Models\Approval;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaprodiController extends Controller
{
    public function dashboard()
    {
        return view('kaprodi.dashboard', [
            'suratDisetujui' => count(Approval::where('user_id', '=', auth()->user()->id)->where('isApproved', '=', true)->get()),
            'suratDitolak' => count(Approval::where('user_id', '=', auth()->user()->id)->where('isApproved', '=', false)->get()),
            'suratMenunggu' => count(Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'diproses')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })->get()->toArray())

        ]);
    }

    public function profilePage()
    {
        return view('kaprodi.profile', [
            'daftarProgramStudi' => ProgramStudi::all()
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $request->validate([
            'username' => 'string|required|alpha_dash',
            'name' => 'string|required',
            'nip' => 'required',
            'email' => 'email|required',
            'program-studi' => 'required'
        ]);

        if ($request->input('username') != $user->username) {
            $request->validate([
                'username' => 'unique:users,username'
            ]);
            $user->update($request->only('username'));
        }

        if ($request->input('nip') != $user->nip) {
            $request->validate([
                'nip' => 'unique:users,nip'
            ]);
            $user->update($request->only('nip'));
        }

        if ($request->input('email') != $user->email) {
            $request->validate([
                'email' => 'unique:users,email'
            ]);
            $user->update($request->only('email'));
            $user->email_verified_at = null;
        }
        // if ($request->hasFile('ttd')) {
        //     $request->validate([
        //         'ttd' => 'file|mimes:png|max:2048'
        //     ]);
        //     $uuid = Uuid::uuid4();
        //     $file = $request->file('ttd');
        //     Storage::disk('public')->put('ttd/' . $uuid, file_get_contents($file));
        //     $user->update(['tandatangan' => 'ttd/' . $uuid]);
        // }
        $user->update($request->only('name'));
        return redirect('/kaprodi/profile')->with('success', 'Sukses mengupdate data');
    }


    public function index()
    {
        return view('admin.users.kaprodi.index');
    }

    public function suratMasuk(Request $request)
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'diproses')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })
            ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);

        if ($request->get('jenis-surat') && $request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('jenis-surat')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        }


        return view('kaprodi.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::all(),
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id == auth()->user()->id) {
            $idJurusan = User::join('program_studi_tables as pst', 'users.program_studi_id', '=', 'pst.id')
                ->join('jurusan_tables as jt', 'pst.jurusan_id', '=', 'jt.id')
                ->where('users.id', $surat->pengaju->id)
                ->select('jt.id')
                ->first();

            return view('kaprodi.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 6)
                    ->where('jurusan_id', $idJurusan->id)
                    ->get()
            ]);
        }
        return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('kaprodi.show-approval', [
            'approval' => $approval,
            'surat' => Surat::join('approvals', 'approvals.surat_id', '=', 'surat_tables.id')
                ->where('approvals.user_id', auth()->user()->id)
                ->where('approvals.id', $approval->id)
                ->first()
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function riwayatPersetujuan(Request $request)
    {

        $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
            ->where('user_id', '=', auth()->user()->id)
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);


        if ($request->get('search') && $request->get('jenis-surat') && $request->get('status')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('approvals.isApproved', $request->get('status') != 'ditolak' ? true : false)
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('status') && $request->get('jenis-surat')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('approvals.isApproved', $request->get('status') != 'ditolak' ? true : false)
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('status') && $request->get('search')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('approvals.isApproved', $request->get('status') != 'ditolak' ? true : false)
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('jenis-surat') && $request->get('search')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('status')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('approvals.isApproved', $request->get('status') != 'ditolak' ? true : false)
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('jenis-surat')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('search')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        }

        return view('kaprodi.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarStatus' => [true => 'Disetujui', false => 'Ditolak'],
        ]);
    }

    public function setujuiSurat(Request $request, Surat $surat)
    {
        // if (!auth()->user()->tandatangan) {
        //     return redirect()->back()->withErrors('Tanda Tangan tidak boleh kosong, silahkan atur terlebih dahulu di profil');
        // }
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;

        // $idJurusan = User::join('program_studi_tables as pst', 'users.program_studi_id', '=', 'pst.id')
        //     ->join('jurusan_tables as jt', 'pst.jurusan_id', '=', 'jt.id')
        //     ->where('users.id', $surat->pengaju->id)
        //     ->select('jt.id')
        //     ->first();
        // $akademik = User::select('id')
        //     ->where('role_id', '=', 6)
        //     ->where('jurusan_id', '=', $idJurusan->id)
        //     ->first();

        $wd1 = User::where('role_id', '=', 5)->first();
        $surat->current_user_id = $request->input('penerima');
        $data = $surat->data;
        if ($data) {
            if (isset($data['private'])) {
                $data['private']['namaWD1'] =  $wd1->name;
                $data['private']['nipWD1'] =  $wd1->nip;
            } else {
                $data['private'] = [
                    'namaWD1' =>  $wd1->name,
                    'nipWD1' =>  $wd1->nip,
                ];
            }
        } else {
            $data = [
                'private' => [
                    'namaWD1' =>  $wd1->name,
                    'nipWD1' =>  $wd1->nip
                ]
            ];
        }
        $surat->data = $data;


        // $surat->penerima_id = $akademik->id;
        // $file = $surat->files;
        // if ($file) {
        //     if (isset($file['private'])) {
        //         $file['private']['ttdKaprodi'] =  'storage/' . auth()->user()->tandatangan;
        //     } else {
        //         $file['private'] = [
        //             'ttdKaprodi' => 'storage/' . auth()->user()->tandatangan
        //         ];
        //     }
        // } else {
        //     $file = [
        //         'private' => [
        //             'ttdKaprodi' => 'storage/' . auth()->user()->tandatangan,
        //         ]
        //     ];
        // }
        // $surat->files = $file;
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
        return redirect('/kaprodi/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }

    public function resetPasswordPage()
    {
        return view('kaprodi.reset-password');
    }

    public function resetPassword(Request $request, User $user)
    {
        if (!Hash::check($request->input('old-password'), $user->password)) {
            return back()->withErrors(['password', 'Password yang anda masukkan salah!']);
        }
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);
        $user->update(['password' => bcrypt($request->input('password'))]);
        return redirect('/kaprodi/profile')->with('success', 'Kata sandi sukses diganti!');
    }
}
