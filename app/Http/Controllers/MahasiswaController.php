<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        return view('mahasiswa.dashboard', [
            'pengajuanSelesai' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'selesai')->get(),
            'pengajuanDitolak' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'ditolak')->get(),
            'pengajuanDiproses' =>  Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'diproses')->where(function ($query) {
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
        return view('mahasiswa.profile', [
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
            $programStudiKode = ProgramStudi::pluck('kode');
            $request->validate([
                'username' =>  [
                    'required',
                    'size:9',
                    'unique:users,username',
                    function ($attribute, $value, $fail) use ($programStudiKode) {
                        // Gunakan callback untuk memeriksa apakah nilai diawali dengan salah satu kode program studi
                        foreach ($programStudiKode as $kode) {
                            if (strpos($value, $kode) === 0) {
                                return; // Validasi berhasil
                            }
                        }

                        // Validasi gagal jika tidak ada kode program studi yang cocok
                        $fail("NPM yang anda masukkan tidak sesuai atau tidak terdaftar dalam program studi di FKIP!");
                    },
                ]
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
        // if ($request->input('program-studi') != $user->program_studi_id) {
        //     if (strpos($user->username, $user->programStudi->kode) !== 0) {

        //         return redirect('/mahasiswa/profile')->with('deleted', 'Program Studi tidak sesuai NPM Anda');
        //     } else {

        //         $user->update(['program_studi_id' => $request->input('program-studi')]);
        //     }
        // }
        $user->update(['program_studi_id' => $request->input('program-studi')]);
        $user->update($request->only('name'));
        return redirect('/mahasiswa/profile')->with('success', 'Sukses mengupdate data');
    }

    public function pengajuanSurat()
    {
        return view('mahasiswa.pengajuan-surat', [
            'daftarJenisSurat' => JenisSurat::where('user_type', 'mahasiswa')->get(),
        ]);
    }

    public function pengajuanLegalisirIjazah()
    {
        $idJurusan = User::join('program_studi_tables as pst', 'users.program_studi_id', '=', 'pst.id')
            ->join('jurusan_tables as jt', 'pst.jurusan_id', '=', 'jt.id')
            ->where('users.id', auth()->user()->id)
            ->select('jt.id')
            ->first();

        return view('mahasiswa.pengajuan-legalisir-ijazah', [
            'jenisSurat' => JenisSurat::where('user_type', 'mahasiswa')->where('slug', 'legalisir-ijazah')->first(),
            'daftarProgramStudi' => ProgramStudi::all(),
            'daftarPenerima' => User::select('id', 'name', 'username')
                ->where('role_id', '=', 6)
                ->where('jurusan_id', $idJurusan->id)
                ->get()
        ]);
    }

    public function riwayatPengajuanSurat(Request $request)
    {
        // dd($request->get('order') != 'asc' ? 'desc' : 'asc');
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

        return view('mahasiswa.riwayat-pengajuan', [
            'daftarPengajuan' => $daftarPengajuan,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarStatus' => ['diproses', 'ditolak', 'selesai'],

        ]);
    }


    public function lihatSurat(Surat $surat)
    {
        return view('mahasiswa.show-surat', [
            'surat' => $surat
        ]);
    }

    public function index()
    {
        return view('admin.users.mahasiswa.index', [
            'daftarMahasiswa' => User::where('role_id', 2)->get()
        ]);
    }

    public function create()
    {
        return view('admin.users.mahasiswa.create');
    }

    public function edit()
    {
        return view('admin.users.mahasiswa.edit');
    }

    public function resetPasswordPage()
    {
        return view('mahasiswa.reset-password');
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
        return redirect('/mahasiswa/profile')->with('success', 'Kata sandi sukses diganti!');
    }
}
