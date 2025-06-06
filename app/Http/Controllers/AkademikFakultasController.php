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

class AkademikFakultasController extends Controller
{
    public function dashboard()
    {
        return view('akademik-fakultas.dashboard', [
            'suratDisetujui' => count(Approval::where('user_id', '=', auth()->user()->id)->where('isApproved', '=', true)->get()),
            'suratDitolak' => count(Approval::where('user_id', '=', auth()->user()->id)->where('isApproved', '=', false)->get()),
            'suratMenunggu' => count(Surat::where('current_user_id', '=', auth()->user()->id)->where('status', 'diproses')->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
            })->get()->toArray())
        ]);
    }

    public function index()
    {
        return view('admin.users.akademik-fakultas.index');
    }

    public function profilePage()
    {
        return view('akademik-fakultas.profile', [
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
        return redirect('/akademik-fakultas/profile')->with('success', 'Sukses mengupdate data');
    }


    public function suratMasuk(Request $request)
    {
        // $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
        //     ->where('current_user_id', '=', auth()->user()->id)->where('status', 'diproses')->where(function ($query) {
        //         $now = Carbon::now();
        //         $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        //     })
        //     ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
        //     ->paginate(10)
        //     ->appends(request()->query());

        $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
            ->where('current_user_id', auth()->user()->id)
            ->where('status', 'diproses')
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereHas('jenisSurat', function ($q) {
                    $q->where('slug', 'legalisir-ijazah'); // Kondisi khusus untuk legalisir ijazah
                })->orWhere(function ($q) use ($now) {
                    $q->whereNull('expired_at')->orWhere('expired_at', '>', $now); // Kondisi default
                });
            })
            ->orderBy('surat_tables.created_at', $request->get('order') != 'asc' ? 'desc' : 'asc')
            ->paginate(10)
            ->appends(request()->query());

        if ($request->get('search') && $request->get('jenis-surat') && $request->get('program-studi')) {
            $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
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
            $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
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
            $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
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
            $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
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
            $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
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
            $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
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
            $daftarSuratMasuk = Surat::with('pengaju', 'pengaju.programStudi')
                ->select('surat_tables.*')
                ->join('jenis_surat_tables', 'jenis_surat_tables.id', '=', 'surat_tables.jenis_surat_id')
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


        return view('akademik-fakultas.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarProgramStudi' => ProgramStudi::all(),
        ]);
    }
    public function showSuratMasuk(Surat $surat)
    {

        if (($surat->jenisSurat->slug == 'legalisir-ijazah' || $surat->jenisSurat->slug == 'legalisir-transkrip' || $surat->jenisSurat->slug = 'legalisir-ijazah-transkrip') && $surat->data['pengiriman'] == 'dikirim') {

            return view('akademik-fakultas.show-legalisir', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 15)
                    ->get()
            ]);
        }

        if (($surat->jenisSurat->slug == 'legalisir-ijazah' || $surat->jenisSurat->slug == 'legalisir-transkrip' || $surat->jenisSurat->slug = 'legalisir-ijazah-transkrip') && $surat->data['pengiriman'] == 'ambil') {

            return view('akademik-fakultas.show-legalisir-diambil', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 15)
                    ->get()
            ]);
        }
        if ($surat->current_user_id == auth()->user()->id) {

            return view('akademik-fakultas.show-surat', [
                'surat' => $surat
            ]);
        }


        return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
    }




    public function setujuiSurat(Request $request, Surat $surat)
    {
        if (($surat->jenisSurat->slug === 'legalisir-ijazah' || $surat->jenisSurat->slug === 'legalisir-transkrip' || $surat->jenisSurat->slug = 'legalisir-ijazah-transkrip') && $surat->data['pengiriman'] == 'dikirim') {

            // if (!auth()->user()->tandatangan) {
            //     return redirect()->back()->withErrors('Stempel tidak boleh kosong, silahkan atur terlebih dahulu di profil');
            // }
            $request->validate([
                // 'no-surat' => 'required|size:4|unique:surat_tables,data->noSurat',
                // 'no-surat' =>  ['required', 'size:4', Rule::unique('surat_tables', 'data->noSurat')->where('jenis_surat_id', $surat->jenisSurat->id)],

                'no-resi' => 'required',
                // 'faktur-jne' => 'file|mimes:jpeg,png,jpg,pdf|max:2048',

            ]);
            // SELECT jt.id FROM users u
            // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
            // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
            $surat->current_user_id = $surat->pengaju_id;
            // $surat->penerima_id = $surat->pengaju_id;
            // $surat->expired_at = null;
            $data = $surat->data;
            // $data['tanggal_selesai'] = formatTimestampToOnlyDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
            // $data['ttdWD1'] = $request->input('ttd') ;
            // $data['stempel'] = $request->input('stempel') ;
            // $data['ttdWD1'] = 'storage/ttd/AOqKQVPwY53QkHoHnDvjs4ljWQE3B0-metaaWx1c3RyYXNpLWthbWFyLWJlcmFudGFrYW4uanBn-.jpg' ;
            // $data['stempel'] = 'storage/ttd/AOqKQVPwY53QkHoHnDvjs4ljWQE3B0-metaaWx1c3RyYXNpLWthbWFyLWJlcmFudGFrYW4uanBn-.jpg';
            $data['noResi'] = $request->input('no-resi');
            $data['tanggalPengiriman'] =  formatTimestampToDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
            $data['note'] = $request->input('note');

            $surat->data = $data;


            $surat->status = 'dikirim';
            // if ($request->hasFile('fakturJNE')) {

            //     $surat->files = [
            //         'fakturJNE' => $request->file('faktur-jne')->store('lampiran'),

            //     ];
            // }

            $surat->save();

            Approval::create([
                'user_id' => auth()->user()->id,
                'surat_id' => $surat->id,
                'isApproved' => true,
                'note' => $request->input('note'),
            ]);

            Mail::to($surat->pengaju->email)->send(new LegalisirDikirim($surat));

            return redirect('/akademik-fakultas/surat-masuk')->with('success', 'Surat berhasil disetujui');
        }

        if (($surat->jenisSurat->slug === 'legalisir-ijazah' || $surat->jenisSurat->slug === 'legalisir-transkrip' || $surat->jenisSurat->slug = 'legalisir-ijazah-transkrip') && $surat->data['pengiriman'] == 'ambil') {

            // if (!auth()->user()->tandatangan) {
            //     return redirect()->back()->withErrors('Stempel tidak boleh kosong, silahkan atur terlebih dahulu di profil');
            // }

            // SELECT jt.id FROM users u
            // JOIN program_studi_tables pst ON pst.id = u.program_studi_id
            // JOIN jurusan_tables jt ON jt.id = pst.jurusan_id ;
            $surat->current_user_id = $surat->pengaju_id;
            // $surat->penerima_id = $surat->pengaju_id;
            $surat->expired_at = null;
            $data = $surat->data;
            // $data['tanggal_selesai'] = formatTimestampToOnlyDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
            // $data['ttdWD1'] = $request->input('ttd') ;
            // $data['stempel'] = $request->input('stempel') ;
            // $data['ttdWD1'] = 'storage/ttd/AOqKQVPwY53QkHoHnDvjs4ljWQE3B0-metaaWx1c3RyYXNpLWthbWFyLWJlcmFudGFrYW4uanBn-.jpg' ;
            // $data['stempel'] = 'storage/ttd/AOqKQVPwY53QkHoHnDvjs4ljWQE3B0-metaaWx1c3RyYXNpLWthbWFyLWJlcmFudGFrYW4uanBn-.jpg';
            $data['note'] = $request->input('note');
            $data['tanggalSelesai'] = formatTimestampToDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));

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

            Mail::to($surat->pengaju->email)->send(new LegalisirDiambil($surat));


            return redirect('/akademik-fakultas/surat-masuk')->with('success', 'Surat berhasil disetujui');
        }
        // if (!auth()->user()->tandatangan) {
        //     return redirect()->back()->withErrors('Stempel tidak boleh kosong, silahkan atur terlebih dahulu di profil');
        // }
        $request->validate([
            // 'no-surat' => 'required|size:4|unique:surat_tables,data->noSurat',
            // 'no-surat' =>  ['required', 'size:4', Rule::unique('surat_tables', 'data->noSurat')->where('jenis_surat_id', $surat->jenisSurat->id)],

            'no-surat' => ['required', 'max:5', Rule::unique('surat_tables', 'data->noSurat')
                ->where(function ($query) {
                    $query->whereYear('created_at', date('Y'));
                })],
        ]);
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
        $data['noSurat'] = $request->input('no-surat') ?? str_pad($surat->id, 4, '0', STR_PAD_LEFT);
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
        Mail::to($surat->pengaju->email)->send(new SuratMahasiswa($surat));
        return redirect('/akademik-fakultas/surat-masuk')->with('success', 'Surat berhasil disetujui');
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

        return view('akademik-fakultas.riwayat-persetujuan', [
            'daftarRiwayatSurat' => $daftarRiwayatSurat,
            'daftarJenisSurat' => JenisSurat::all(),
            'daftarStatus' => [true => 'Disetujui', false => 'Ditolak'],
        ]);
    }


    public function showApproval(Approval $approval)
    {
        // if ($surat->current_user_id == auth()->user()->id) {
        if (($approval->surat->jenisSurat->slug == 'legalisir-ijazah' || $approval->surat->jenisSurat->slug == 'legalisir-transkrip' || $approval->surat->jenisSurat->slug = 'legalisir-ijazah-transkrip') && $approval->surat->data['pengiriman'] == 'dikirim') {
            return view('akademik-fakultas.show-approval-legalisir', [
                'approval' => $approval,
                'surat' => Surat::join('approvals', 'approvals.surat_id', '=', 'surat_tables.id')
                    ->where('approvals.user_id', auth()->user()->id)
                    ->where('approvals.id', $approval->id)
                    ->first()
            ]);
        }

        if (($approval->surat->jenisSurat->slug == 'legalisir-ijazah' || $approval->surat->jenisSurat->slug == 'legalisir-transkrip' || $approval->surat->jenisSurat->slug = 'legalisir-ijazah-transkrip') && $approval->surat->data['pengiriman'] == 'ambil') {
            return view('akademik-fakultas.show-approval-legalisir-diambil', [
                'approval' => $approval,
                'surat' => Surat::join('approvals', 'approvals.surat_id', '=', 'surat_tables.id')
                    ->where('approvals.user_id', auth()->user()->id)
                    ->where('approvals.id', $approval->id)
                    ->first()
            ]);
        }


        return view('akademik-fakultas.show-approval', [
            'approval' => $approval,
            'surat' => Surat::join('approvals', 'approvals.surat_id', '=', 'surat_tables.id')
                ->where('approvals.user_id', auth()->user()->id)
                ->where('approvals.id', $approval->id)
                ->first()
        ]);
        // }
        // return redirect('/staff/surat-masuk')->with('success', 'Surat berhasil disetujui');
    }




    public function confirmTolakSurat(Surat $surat)
    {
        return view('akademik-fakultas.confirm-tolak', [
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
        return redirect('/akademik-fakultas/surat-masuk')->with('success', 'Surat berhasil ditolak');
    }

    public function resetPasswordPage()
    {
        return view('akademik-fakultas.reset-password');
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
        return redirect('/akademik-fakultas/profile')->with('success', 'Kata sandi sukses diganti!');
    }
}
