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

    /**
     * Cek apakah nama akun masih placeholder bawaan (mis. "Kaprodi S1 ...").
     */
    private function isKaprodiPlaceholderName(?string $name): bool
    {
        $name = trim((string) $name);
        return $name === '' || preg_match('/^Kaprodi\b/i', $name) === 1;
    }

    /**
     * Cek apakah NIP masih kosong / hanya titik-titik.
     */
    private function isKaprodiPlaceholderNip(?string $nip): bool
    {
        $nip = trim((string) $nip);
        return $nip === '' || trim($nip, '.') === '';
    }

    /**
     * Hard-block: pastikan Nama & NIP penandatangan valid sebelum Kaprodi menyetujui.
     */
    private function validateKaprodiSigner(Request $request): void
    {
        $request->validate([
            'nama_kaprodi' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($this->isKaprodiPlaceholderName($value)) {
                        $fail('Nama penandatangan belum valid. Mohon isi nama lengkap & gelar Anda (bukan "Kaprodi ...").');
                    }
                },
            ],
            'nip_kaprodi' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if ($this->isKaprodiPlaceholderNip($value)) {
                        $fail('NIP penandatangan belum valid. Mohon isi NIP Anda.');
                    }
                },
            ],
        ]);
    }

    /**
     * Ambil Nama & NIP penandatangan dari input (fallback ke akun), dan
     * opsional simpan ke profil akun bila user mencentang "simpan_ke_profil".
     *
     * @return array{0: string, 1: string} [nama, nip]
     */
    private function resolveKaprodiSigner(Request $request): array
    {
        $user = auth()->user();
        $nama = trim((string) $request->input('nama_kaprodi', ''));
        $nip = trim((string) $request->input('nip_kaprodi', ''));

        if ($nama === '') {
            $nama = (string) $user->name;
        }
        if ($nip === '') {
            $nip = (string) ($user->nip ?: $user->username);
        }

        if ($request->boolean('simpan_ke_profil')) {
            $updates = [];
            if ($nama !== (string) $user->name) {
                $updates['name'] = $nama;
            }
            if ($nip !== (string) $user->nip) {
                $updates['nip'] = $nip;
            }
            if (!empty($updates)) {
                $user->update($updates);
            }
        }

        return [$nama, $nip];
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


        return view('kaprodi.surat-masuk', [
            'daftarSuratMasuk' => $daftarSuratMasuk,
            'daftarJenisSurat' => JenisSurat::all(),
        ]);
    }

    public function showSuratMasuk(Surat $surat)
    {
        if ($surat->current_user_id != auth()->user()->id) {

            return redirect()->back()->with('deleted', 'Anda tidak dapat mengakses halaman yang dituju');
        }

        if ($surat->jenisSurat->user_type == 'mahasiswa') {
            if (in_array($surat->jenisSurat->slug, ['surat-pencairan-dana-mahasiswa', 'surat-peminjaman-ruang-mahasiswa'])) {
                return view('kaprodi.show-surat', [
                    'surat' => $surat,
                    'daftarPenerima' => User::select('id', 'name', 'username', 'role_id')
                        ->where('role_id', '=', User::ROLE_STAFF_DEKAN)
                        ->get()
                ]);
            }

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

        if (in_array($surat->jenisSurat->slug, ['surat-pencairan-dana', 'surat-peminjaman-ruang', 'surat-permohonan-narasumber', 'surat-cuti-mahasiswa', 'surat-izin-observasi', 'surat-penundaan-pembayaran-ukt'])) {
            return view('kaprodi.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username', 'role_id')
                    ->where('role_id', '=', User::ROLE_STAFF_DEKAN)
                    ->get()
            ]);
        }

        if (($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas') || ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas-kelompok')) {
            return view('kaprodi.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [14])
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if (($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'berita-acara-nilai')) {
            return view('kaprodi.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [5])
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if (($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-pengajuan-atk')) {
            return view('kaprodi.show-surat', [
                'surat' => $surat,
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [17])
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }
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

        $isKaprodiSigned = in_array($surat->jenisSurat->slug, [
            'surat-permohonan-narasumber',
            'surat-peminjaman-ruang',
            'surat-peminjaman-ruang-mahasiswa',
            'surat-pencairan-dana',
            'surat-pencairan-dana-mahasiswa'
        ]);

        $wd1 = User::where('role_id', '=', 5)->first();
        $surat->current_user_id = $request->input('penerima');
        $data = $surat->data;
        if (!isset($data['private'])) {
            $data['private'] = [];
        }

        if (!$isKaprodiSigned && $wd1) {
            $data['private']['namaWD1'] =  $wd1->name;
            $data['private']['nipWD1'] =  $wd1->nip;
        }

        if ($isKaprodiSigned) {
            $this->validateKaprodiSigner($request);
            [$namaKaprodi, $nipKaprodi] = $this->resolveKaprodiSigner($request);
        } else {
            $namaKaprodi = auth()->user()->name;
            $nipKaprodi = auth()->user()->nip ?: auth()->user()->username;
        }

        $data['private']['namaKaprodi'] = $namaKaprodi;
        $data['private']['nipKaprodi'] = $nipKaprodi;
        $data['private']['deskripsiKaprodi'] = 'Koordinator Program Studi';

        if (isset($data['private']['stepper'])) {
            $data['private']['stepper'][] = auth()->user()->role->id;
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

    public function setujuiSuratStaff(Request $request, Surat $surat)
    {

        if ($surat->jenisSurat->slug == 'berita-acara-nilai') {
            $surat->current_user_id = $request->input('penerima');
            $surat->save();

            Approval::create([
                'user_id' => auth()->user()->id,
                'surat_id' => $surat->id,
                'isApproved' => true,
                'note' => 'setuju',
            ]);
            return redirect('kaprodi/surat-masuk')->with('success', 'Surat berhasil disetujui');
        }

        if (in_array($surat->jenisSurat->slug, ['surat-pencairan-dana', 'surat-peminjaman-ruang', 'surat-permohonan-narasumber', 'surat-cuti-mahasiswa', 'surat-izin-observasi', 'surat-penundaan-pembayaran-ukt'])) {
            $this->validateKaprodiSigner($request);
            [$namaKaprodi, $nipKaprodi] = $this->resolveKaprodiSigner($request);

            $surat->current_user_id = $request->input('penerima');
            $data = $surat->data;
            if (!isset($data['private'])) {
                $data['private'] = [];
            }
            $data['private']['namaKaprodi'] = $namaKaprodi;
            $data['private']['nipKaprodi'] = $nipKaprodi;
            $data['private']['deskripsiKaprodi'] = 'Koordinator Program Studi';
            if (isset($data['private']['stepper'])) {
                $data['private']['stepper'][] = auth()->user()->role->id;
            }
            $surat->data = $data;
            $surat->save();

            Approval::create([
                'user_id' => auth()->user()->id,
                'surat_id' => $surat->id,
                'isApproved' => true,
                'note' => 'setuju',
            ]);
            return redirect('kaprodi/surat-masuk')->with('success', 'Surat berhasil disetujui');
        }

        if ($surat->jenisSurat->slug == 'surat-pengajuan-atk') {
            $surat->current_user_id = $request->input('penerima');
            $surat->save();

            Approval::create([
                'user_id' => auth()->user()->id,
                'surat_id' => $surat->id,
                'isApproved' => true,
                'note' => 'setuju',
            ]);
            return redirect('kaprodi/surat-masuk')->with('success', 'Surat berhasil disetujui');
        }

        if ($surat->jenisSurat->slug == 'surat-tugas' || $surat->jenisSurat->slug == 'surat-tugas-kelompok') {
            $surat->current_user_id = $request->input('penerima');
            $data = $surat->data;

            // Jika penerimanya adalah staff dekan langsung, maka surat di ttd oleh dekan
            $rolePenerima = User::where('id', $surat->current_user_id)->first()->role->id;
            if ($rolePenerima == 14) {

                if ($data) {
                    if (isset($data['private'])) {
                        $data['private']['namaDekan'] =  auth()->user()->name;
                        $data['private']['nipDekan'] =  auth()->user()->nip;
                        $data['private']['deskripsiDekan'] =  auth()->user()->role->description;
                        $data['private']['stepper'][] = auth()->user()->role->id;
                    } else {
                        $data['private'] = [
                            'namaDekan' =>  auth()->user()->name,
                            'nipDekan' =>  auth()->user()->nip,
                            'deskripsiDekan' =>  auth()->user()->role->description,
                            'stepper' => [auth()->user()->role->id]

                        ];
                    }
                } else {
                    $data = [
                        'private' => [
                            'namaDekan' =>  auth()->user()->name,
                            'nipDekan' =>  auth()->user()->nip,
                            'deskripsiDekan' =>  auth()->user()->role->description,
                            'stepper' => [auth()->user()->role->id]
                        ]
                    ];
                }
            } else {
                // Jika bukan staff dekan tujuan kirimnya, maka di ttd WD nantinya
                if ($data) {
                    if (isset($data['private'])) {
                        $data['private']['stepper'][] = auth()->user()->role->id;
                    } else {
                        $data['private'] = [
                            'stepper' => [auth()->user()->role->id]

                        ];
                    }
                } else {
                    $data = [
                        'private' => [
                            'stepper' => [auth()->user()->role->id]
                        ]
                    ];
                }
            }


            $surat->data = $data;

            $surat->save();

            Approval::create([
                'user_id' => auth()->user()->id,
                'surat_id' => $surat->id,
                'isApproved' => true,
                'note' => 'setuju',
            ]);
            return redirect('kaprodi/surat-masuk')->with('success', 'Surat berhasil disetujui');
        }
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
        if (isset($data['private']['stepper'])) {
            $data['private']['stepper'][] = auth()->user()->role->id;
        }
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
