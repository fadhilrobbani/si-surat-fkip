<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WDController extends Controller
{
    public function dashboard()
    {
        return view('wd.dashboard',[
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
        return view('admin.users.wd.index');
    }

    public function profilePage(){
        return view('wd.profile');
    }

    public function updateProfile(Request $request, User $user){
        $request->validate([
            'username' => 'string|required',
            'name' => 'string|required',
            'email' =>'email|required',
        ]);

        if($request->input('username') != $user->username){
            $request->validate([
                'username' => 'unique:users,username'
            ]);
            $user->update($request->only('username'));
        }

        if($request->input('email') != $user->email){
            $request->validate([
                'email' => 'unique:users,email'
            ]);
            $user->update($request->only('email'));
            $user->email_verified_at = null;
        }
        $user->update($request->only('name'));
        return redirect('/wd/profile')->with('success','Sukses mengupdate data');
    }


    public function suratMasuk(Request $request)
    {

        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })
            ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);

        if ($request->get('search') && $request->get('jenis-surat') && $request->get('program-studi')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'on_process')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('program-studi') && $request->get('jenis-surat')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'on_process')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('program-studi') && $request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'on_process')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('jenis-surat') && $request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'on_process')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('program-studi')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
            ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
            ->where('current_user_id', '=', auth()->user()->id)
            ->where('status', 'on_process')
            ->where(function ($query) {
                $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.program_studi_id', $request->get('program-studi'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
                // dd($daftarSuratMasuk);
        } elseif ($request->get('jenis-surat')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'on_process')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('surat_tables.jenis_surat_id', $request->get('jenis-surat'))
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        } elseif ($request->get('search')) {
            $daftarSuratMasuk = Surat::join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
                ->join('users', 'users.id', '=', 'surat_tables.pengaju_id')
                ->join('program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
                ->where('current_user_id', '=', auth()->user()->id)
                ->where('status', 'on_process')
                ->where(function ($query) {
                    $now = Carbon::now();
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
                })
                ->where('users.username', 'LIKE', '%' . $request->get('search') . '%')
                ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
                ->paginate(10);
        }


        return view('wd.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarProgramStudi' => ProgramStudi::all(),
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

            return view('wd.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 6)
                    ->where('jurusan_id' , $idJurusan->id)
                    ->get()
            ]);
        }
        return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
    }

    public function setujuiSurat(Request $request,Surat $surat)
    {
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
        // $jurusan = User::select('jurusan_tables.id')
        //     ->join('program_studi_tables as program_studi_tables', 'program_studi_tables.id', '=', 'users.program_studi_id')
        //     ->join('jurusan_tables as jurusan_tables', 'jurusan_tables.id', '=', 'program_studi_tables.jurusan_id')
        //     ->first();
        // dd($surat->pengaju->id);

        // $idJurusan = User::join('program_studi_tables as pst', 'users.program_studi_id', '=', 'pst.id')
        //     ->join('jurusan_tables as jt', 'pst.jurusan_id', '=', 'jt.id')
        //     ->where('users.id', $surat->pengaju->id)
        //     ->select('jt.id')
        //     ->first();
        // $akademik = User::select('id')
        //     ->where('role_id', '=', 6)
        //     ->where('jurusan_id', '=', $idJurusan->id)
        //     ->first();

        //kita buat wd1 bisa memilih penerimanya selain di data misal dari request wd1, tapi sementara manual dulu
        $surat->current_user_id = $request->input('penerima');
        // $surat->current_user_id = $surat->penerima_id;
        // $surat->penerima_id = $surat->pengaju_id;
        $surat->save();

        Approval::create([
            'user_id' => auth()->user()->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => 'setuju',
        ]);
        return redirect('wd/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }
    public function riwayatPersetujuan(Request $request)
    {
        $daftarRiwayatSurat = Approval::where('user_id', '=', auth()->user()->id)
        ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
        ->paginate(10);


        if ($request->get('search') && $request->get('jenis-surat') && $request->get('status')){
            $daftarRiwayatSurat = Approval::join('surat_tables','surat_tables.id','=','approvals.surat_id')
            ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users','users.id','=','surat_tables.pengaju_id')
            ->where('users.username', 'LIKE', '%'.$request->get('search').'%')
            ->where('approvals.isApproved',$request->get('status') != 'ditolak' ? true : false)
            ->where('approvals.user_id', '=', auth()->user()->id)
            ->where('surat_tables.jenis_surat_id',$request->get('jenis-surat'))
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);
        }

        elseif ($request->get('status') && $request->get('jenis-surat')) {
            $daftarRiwayatSurat = Approval::join('surat_tables','surat_tables.id','=','approvals.surat_id')
            ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users','users.id','=','surat_tables.pengaju_id')
            ->where('approvals.isApproved',$request->get('status') != 'ditolak' ? true : false)
            ->where('approvals.user_id', '=', auth()->user()->id)
            ->where('surat_tables.jenis_surat_id',$request->get('jenis-surat'))
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);
        }

        elseif ($request->get('status') && $request->get('search')) {
            $daftarRiwayatSurat = Approval::join('surat_tables','surat_tables.id','=','approvals.surat_id')
            ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users','users.id','=','surat_tables.pengaju_id')
            ->where('users.username', 'LIKE', '%'.$request->get('search').'%')
            ->where('approvals.isApproved',$request->get('status')!= 'ditolak' ? true : false)
            ->where('approvals.user_id', '=', auth()->user()->id)
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);
        }

        elseif ($request->get('jenis-surat') && $request->get('search')) {
            $daftarRiwayatSurat = Approval::join('surat_tables','surat_tables.id','=','approvals.surat_id')
            ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users','users.id','=','surat_tables.pengaju_id')
            ->where('users.username', 'LIKE', '%'.$request->get('search').'%')
            ->where('approvals.user_id', '=', auth()->user()->id)
            ->where('surat_tables.jenis_surat_id',$request->get('jenis-surat'))
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);
        }

        elseif ($request->get('status')) {
            $daftarRiwayatSurat = Approval::join('surat_tables','surat_tables.id','=','approvals.surat_id')
            ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users','users.id','=','surat_tables.pengaju_id')
            ->where('approvals.isApproved',$request->get('status')!= 'ditolak' ? true : false)
            ->where('approvals.user_id', '=', auth()->user()->id)
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);
        }

        elseif ($request->get('jenis-surat')) {
            $daftarRiwayatSurat = Approval::join('surat_tables','surat_tables.id','=','approvals.surat_id')
            ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users','users.id','=','surat_tables.pengaju_id')
            ->where('approvals.user_id', '=', auth()->user()->id)
            ->where('surat_tables.jenis_surat_id',$request->get('jenis-surat'))
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);
        }

        elseif ($request->get('search')) {
            $daftarRiwayatSurat = Approval::join('surat_tables','surat_tables.id','=','approvals.surat_id')
            ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
            ->join('users','users.id','=','surat_tables.pengaju_id')
            ->where('users.username', 'LIKE', '%'.$request->get('search').'%')
            ->where('approvals.user_id', '=', auth()->user()->id)
            ->orderBy('approvals.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10);
        }

        return view('wd.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarStatus' => [true => 'Disetujui',false => 'Ditolak'],
        ]);
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('wd.show-approval', [
            'approval' => $approval
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }


    public function confirmTolakSurat(Surat $surat)
    {
        return view('wd.confirm-tolak', [
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
        return redirect('/wd/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }
}
