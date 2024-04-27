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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class StaffNilaiController extends Controller
{
    public function dashboard()
    {
        return view('staff-nilai.dashboard', [
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
        return view('staff-nilai.profile');
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

        $user->update($request->only('name'));
        return redirect('/staff-nilai/profile')->with('success', 'Sukses mengupdate data');
    }


    public function suratMasuk(Request $request)
    {

        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'diproses')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })
            ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10)
            ->appends(request()->query());

        if ($request->get('search') && $request->get('jenis-surat') && $request->get('program-studi')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('program-studi') && $request->get('jenis-surat')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('program-studi') && $request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('jenis-surat') && $request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('program-studi')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
            // dd($daftarSuratMasuk);
        } elseif ($request->get('jenis-surat')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->select('surat_tables.*')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'diproses')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        }


        return view('staff-nilai.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::where('slug', '=', 'berita-acara-nilai')->get(),
            'daftarProgramStudi' => ProgramStudi::all(),
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id != auth()->user()->id) {

            return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
        }


        if ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'berita-acara-nilai') {

            return view('staff-nilai.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 7)
                    ->get()
            ]);
        }
    }

    public function setujuiSurat(Request $request, Surat $surat)
    {
        // if (!auth()->user()->tandatangan) {
        //     return redirect()->back()->withErrors('Stempel tidak boleh kosong, silahkan atur terlebih dahulu di profil');
        // }
        // $request->validate([
        //     // 'no-surat' => 'required|size:4|unique:surat_tables,data->noSurat',
        //     // 'no-surat' =>  ['required', 'size:4', Rule::unique('surat_tables', 'data->noSurat')->where('jenis_surat_id', $surat->jenisSurat->id)],

        //     'no-surat' => ['required', 'size:4', Rule::unique('surat_tables', 'data->noSurat')
        //         ->where(function ($query) {
        //             $query->whereYear('created_at', date('Y'));
        //         })],
        // ]);
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
        $surat->current_user_id = $surat->pengaju_id;
        // $surat->penerima_id = $surat->pengaju_id;
        $surat->expired_at = null;
        $data = $surat->data;
        $data['tanggal_selesai'] = formatTimestampToOnlyDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
        // $data['ttdWD1'] = $request->input('ttd') ;
        // $data['stempel'] = $request->input('stempel') ;
        // $data['ttdWD1'] = 'storage/ttd/AOqKQVPwY53QkHoHnDvjs4ljWQE3B0-metaaWx1c3RyYXNpLWthbWFyLWJlcmFudGFrYW4uanBn-.jpg' ;
        // $data['stempel'] = 'storage/ttd/AOqKQVPwY53QkHoHnDvjs4ljWQE3B0-metaaWx1c3RyYXNpLWthbWFyLWJlcmFudGFrYW4uanBn-.jpg';
        // $data['noSurat'] = $request->input('no-surat') ?? str_pad($surat->id, 4, '0', STR_PAD_LEFT);
        $data['note'] = $request->input('note');
        $surat->data = $data;
        // $file = $surat->files;
        // if ($file) {
        //     if (isset($file['private'])) {
        //         $file['private']['stempel'] =  'storage/' . auth()->user()->tandatangan;
        //     } else {
        //         $file['private'] = [
        //             'stempel' => 'storage/' . auth()->user()->tandatangan
        //         ];
        //     }
        // } else {
        //     $file = [
        //         'private' => [
        //             'stempel' => 'storage/' . auth()->user()->tandatangan,
        //         ]
        //     ];
        // }
        // $surat->files = $file;
        $surat->status = 'selesai';
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => $request->input('note'),
        ]);
        // Mail::to($surat->pengaju->email)->send(new SuratMahasiswa($surat));
        return redirect('/staff-nilai/surat-masuk')->with('success', 'Surat berhasil disetujui');
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
        return redirect('/staff-nilai/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }

    public function confirmTolakSurat(Surat $surat)
    {
        return view('staff-nilai.confirm-tolak', [
            'surat' => $surat
        ]);
    }

    public function riwayatPersetujuan(Request $request)
    {
        $keyword = $request->get('search');
        $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
            ->where('user_id', '=', auth()->user()->id)
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10)
            ->appends(request()->query());


        if ($request->get('search') && $request->get('jenis-surat') && $request->get('status')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                // ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where(function (Builder $query) use ($keyword) {

                    return $query->where('users.username', 'LIKE', '%' .  $keyword . '%')
                        ->orWhere('surat_tables.data->noSurat', 'LIKE', '%' .  $keyword . '%');
                })
                ->where('approvals.isApproved', $request->get('status') != 'ditolak' ? true : false)
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
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
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('status') && $request->get('search')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where(function (Builder $query) use ($keyword) {

                    return $query->where('users.username', 'LIKE', '%' .  $keyword . '%')
                        ->orWhere('surat_tables.data->noSurat', 'LIKE', '%' .  $keyword . '%');
                })
                ->where('approvals.isApproved', $request->get('status') != 'ditolak' ? true : false)
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('jenis-surat') && $request->get('search')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where(function (Builder $query) use ($keyword) {

                    return $query->where('users.username', 'LIKE', '%' .  $keyword . '%')
                        ->orWhere('surat_tables.data->noSurat', 'LIKE', '%' .  $keyword . '%');
                })
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('status')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('approvals.isApproved', $request->get('status') != 'ditolak' ? true : false)
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('jenis-surat')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('search')) {
            $daftarRiwayatSurat = Approval::with('surat', 'surat.pengaju', 'surat.jenisSurat')
                ->select('approvals.*')
                ->join('surat_tables', 'surat_tables.id', '=', 'approvals.surat_id')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->where(function (Builder $query) use ($keyword) {

                    return $query->where('users.username', 'LIKE', '%' .  $keyword . '%')
                        ->orWhere('surat_tables.data->noSurat', 'LIKE', '%' .  $keyword . '%');
                })
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        }

        return view('staff-nilai.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat,
            'daftarJenisSurat' => JenisSurat::where('slug', 'berita-acara-nilai')->get(),
            'daftarStatus' => [true => 'Disetujui', false => 'Ditolak'],
        ]);
    }

    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('staff-nilai.show-approval', [
            'approval' => $approval,
            'surat' => Surat::join('approvals', 'approvals.surat_id', '=', 'surat_tables.id')
                ->where('approvals.user_id', auth()->user()->id)
                ->where('approvals.id', $approval->id)
                ->first()
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function resetPasswordPage()
    {
        return view('staff-nilai.reset-password');
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
        return redirect('/staff-nilai/profile')->with('success', 'Kata sandi sukses diganti!');
    }
}
