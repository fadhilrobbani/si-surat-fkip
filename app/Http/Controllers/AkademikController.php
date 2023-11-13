<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Surat;
use App\Models\Jurusan;
use App\Models\Approval;
use App\Models\JenisSurat;
use App\Mail\SuratMahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function profilePage(){
        return view('akademik.profile',[
           'daftarJurusan' => Jurusan::all()
        ]);
    }

    public function updateProfile(Request $request, User $user){
        $request->validate([
            'username' => 'string|required',
            'name' => 'string|required',
            'email' =>'email|required',
            'jurusan' => 'required'
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
        $user->update($request->only('name','jurusan'));
        return redirect('/akademik/profile')->with('success','Sukses mengupdate data');
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


        return view('akademik.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarProgramStudi' => ProgramStudi::all(),
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
        Mail::to($surat->pengaju->email)->send(new SuratMahasiswa($surat));
        return redirect('/akademik/surat-masuk')->with('success', 'Surat berhasil disetujui');
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

        return view('akademik.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarStatus' => [true => 'Disetujui',false => 'Ditolak'],
        ]);
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('akademik.show-approval', [
            'approval' => $approval,
            'surat' => Surat::join('approvals','approvals.surat_id','=','surat_tables.id')
            ->where('approvals.user_id', auth()->user()->id)
            ->where('approvals.id', $approval->id)
            ->first()
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
