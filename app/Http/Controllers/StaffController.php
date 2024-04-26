<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function dashboard()
    {
        return view('staff.dashboard', [

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
        return view('staff.profile', [
            'daftarProgramStudi' => ProgramStudi::all()
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $request->validate([
            'username' => 'string|required|alpha_dash',
            'name' => 'string|required',
            'email' => 'email|required',
            'program-studi' => 'required'
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
        // if($request->input('program-studi') != $user->program_studi_id ){
        //     if(strpos($user->username, $user->programStudi->kode) !== 0){

        //         return redirect('/mahasiswa/profile')->with('deleted', 'Program Studi tidak sesuai NPM Anda');
        //     }else{

        //         $user->update(['program_studi_id' => $request->input('program-studi')]);
        //     }
        // }
        $user->update($request->only('name'));
        return redirect('/staff/profile')->with('success', 'Sukses mengupdate data');
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
                ->paginate(10)
                ->appends(request()->query());
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
                ->paginate(10)
                ->appends(request()->query());
        } elseif ($request->get('search')) {
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
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        }


        return view('staff.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::all(),
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id == auth()->user()->id) {

            return view('staff.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::with('programStudi')
                    ->select('id', 'name', 'username')
                    ->where('role_id', '=', 4)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }
        return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('staff.show-approval', [
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
            ->paginate(10)
            ->appends(request()->query());


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
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
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
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
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
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('approvals.user_id', '=', auth()->user()->id)
                ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10)
                ->appends(request()->query());
        }

        return view('staff.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarStatus' => [true => 'Disetujui', false => 'Ditolak'],
        ]);
    }

    public function setujuiSurat(Request $request, Surat $surat)
    {
        $wd1 = User::select('id')
            ->where('role_id', '=', 5)
            ->first();

        $surat->current_user_id = $request->input('penerima');
        // $surat->penerima_id = $wd1->id;
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => 'setuju',
        ]);
        return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function index()
    {
        return view('admin.users.staff.index');
    }


    public function confirmTolakSurat(Surat $surat)
    {
        return view('staff.confirm-tolak', [
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
        return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }

    public function resetPasswordPage()
    {
        return view('staff.reset-password');
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
        return redirect('/staff/profile')->with('success', 'Kata sandi sukses diganti!');
    }

    public function pengajuanSurat()
    {
        return view('staff.pengajuan-surat', [
            'daftarJenisSurat' => JenisSurat::where('user_type', 'staff')->get(),
        ]);
    }

    public function riwayatPengajuanSurat(Request $request)
    {
        $daftarPengajuan = Surat::with('jenisSurat')
            ->where('pengaju_id', '=', auth()->user()->id)
            ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10)
            ->appends(request()->query());;

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
                ->where('surat_tables.status',  $request->get('status') == 'expired' ? 'diproses' : $request->get('status'))
                ->where(function ($query) {
                    $now = Carbon::now();
                    if (request()->get('status') == 'expired') {

                        $query->where('expired_at', '<', $now);
                    } elseif (request()->get('status') == 'diproses') {
                        $query->where('expired_at', '>', $now);
                    }
                })
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

        return view('staff.riwayat-pengajuan', [
            'daftarPengajuan' => $daftarPengajuan,
            'daftarJenisSurat' => JenisSurat::where('user_type', '=', 'staff')->get(),
            'daftarStatus' => ['diproses', 'ditolak', 'selesai'],

        ]);
    }

    public function showDetailPengajuanSuratByStaff(Surat $surat)
    {
        return view('staff.show-surat', [
            'surat' => $surat
        ]);
    }
}
