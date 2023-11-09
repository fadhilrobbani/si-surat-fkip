<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KaprodiController extends Controller
{
    public function dashboard()
    {
        return view('kaprodi.dashboard',[
            'suratDisetujui' => count(Approval::where('user_id', '=', auth()->user()->id)->where('isApproved','=',true)->get()) ,
            'suratDitolak' => count( Approval::where('user_id', '=', auth()->user()->id)->where('isApproved','=',false)->get()),
            'suratMenunggu' => count(Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })->get()->toArray())

        ]);
    }

    public function profilePage(){
        return view('kaprodi.profile',[
            'daftarProgramStudi' => ProgramStudi::all()
        ]);
    }

    public function updateProfile(Request $request, User $user){
        $request->validate([
            'username' => 'string|required',
            'name' => 'string|required',
            'email' =>'email|required',
            'program-studi' => 'required'
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
        $user->update($request->only('name','program-studi'));
        return redirect('/kaprodi/profile')->with('success','Sukses mengupdate data');
    }


    public function index()
    {
        return view('admin.users.kaprodi.index');
    }

    public function suratMasuk()
    {
        $daftarSuratMasuk = Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'on_process')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })->latest()->paginate(10);
        return view('kaprodi.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id == auth()->user()->id) {

            return view('kaprodi.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 5)
                    ->get()
            ]);
        }
        return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {

        return view('kaprodi.show-approval', [
            'approval' => $approval
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }

    public function riwayatPersetujuan()
    {
        $daftarRiwayatSurat = Approval::where('user_id', '=', auth()->user()->id)->latest()->paginate(10);
        return view('kaprodi.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat
        ]);
    }

    public function setujuiSurat(Request $request,Surat $surat)
    {
        // SELECT jt.id FROM users u
        // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
        // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
        $idJurusan = User::join('program_studi_tables as pst', 'users.program_studi_id', '=', 'pst.id')
            ->join('jurusan_tables as jt', 'pst.jurusan_id', '=', 'jt.id')
            ->where('users.id', $surat->pengaju->id)
            ->select('jt.id')
            ->first();
        $akademik = User::select('id')
            ->where('role_id', '=', 6)
            ->where('jurusan_id', '=', $idJurusan->id)
            ->first();

        $surat->current_user_id = $request->input('penerima');
        // $surat->penerima_id = $akademik->id;
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
        return redirect('/kaprodi/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }
}
