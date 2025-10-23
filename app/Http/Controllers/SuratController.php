<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    public function index()
    {
        return view('admin.surat.index');
    }

    public function create(JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'surat-keterangan-alumni') {
            return view('mahasiswa.formsurat.form-keterangan-alumni', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()

            ]);
        }

        if ($jenisSurat->slug == 'surat-keterangan-lulus') {
            return view('mahasiswa.formsurat.form-keterangan-lulus', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-keterangan-pernah-kuliah') {
            return view('mahasiswa.formsurat.form-keterangan-pernah-kuliah', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-aktif-kuliah') {
            return view('mahasiswa.formsurat.form-keterangan-aktif-kuliah', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-keterangan-eligible-pin') {
            return view('mahasiswa.formsurat.form-keterangan-eligible-pin', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-permohonan-izin-penelitian-mahasiswa') {
            return view('mahasiswa.formsurat.form-permohonan-izin-penelitian', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-permohonan-izin-prapenelitian-mahasiswa') {
            return view('mahasiswa.formsurat.form-permohonan-izin-prapenelitian', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-keterangan-kesalahan-ijazah') {
            return view('mahasiswa.formsurat.form-keterangan-kesalahan-ijazah', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-rekomendasi-mbkm') {
            return view('mahasiswa.formsurat.form-rekomendasi-mbkm', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-pengantar-pembayaran-uang-yudisium') {
            return view('mahasiswa.formsurat.form-pengantar-pembayaran-uang-yudisium', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        // SURAT STAFF

        if ($jenisSurat->slug == 'berita-acara-nilai') {
            return view('staff.formsurat.form-berita-acara-nilai', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 4)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-tugas') {
            return view('staff.formsurat.form-surat-tugas', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [4])
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-tugas-kelompok') {
            return view('staff.formsurat.form-surat-tugas-kelompok', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [4])
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-pengajuan-atk') {
            return view('staff.formsurat.form-surat-pengajuan-atk', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [4])
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        // AKADEMIK
        if ($jenisSurat->slug == 'surat-pengajuan-atk-akademik') {
            return view('akademik.formsurat.form-surat-pengajuan-atk', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 17) // Langsung ke Kabag
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-pengajuan-atk-akademik-fakultas') {
            return view('akademik-fakultas.formsurat.form-surat-pengajuan-atk', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 17) // Langsung ke Kabag
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-pengajuan-atk-kemahasiswaan') {
            return view('kemahasiswaan.formsurat.form-surat-pengajuan-atk', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 17) // Langsung ke Kabag
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        //staff dekan
        if ($jenisSurat->slug == 'surat-keluar') {
            return view('staff-dekan.formsurat.form-surat-keluar', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [8, 5, 9, 10])
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-tugas-from-staff-dekan') {
            return view('staff-dekan.formsurat.form-surat-tugas-from-staff-dekan', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [8])
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-tugas-kelompok-from-staff-dekan') {
            return view('staff-dekan.formsurat.form-surat-tugas-kelompok-from-staff-dekan', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [8])
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-pengajuan-atk-tata-usaha') {
            return view('tata-usaha.formsurat.form-surat-pengajuan-atk', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 17) // Langsung ke Kabag
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-pengajuan-atk-unit-kerjasama') {
            return view('unit-kerjasama.formsurat.form-surat-pengajuan-atk', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 17) // Langsung ke Kabag
                    ->orderBy('username', 'asc')
                    ->get()
            ]);
        }
        return abort(404);
    }


    public function store(Request $request, JenisSurat $jenisSurat)
    {

        if ($jenisSurat->slug == 'surat-keterangan-lulus') {

            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'tempat-lahir' => 'required',
                'tanggal-lahir' => 'required|date',
                'jenis-ujian' => 'required',
                'gelar' => 'required',
                'tanggal-ujian' => 'required|date',
                'periode-wisuda' => 'required|numeric',
                'tanggal-wisuda' => 'required|date_format:Y-m',
                'bukti-lulus' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'tempatLahir' => $request->input('tempat-lahir'),
                'tanggalLahir' => formatTimestampToOnlyDateIndonesian($request->input('tanggal-lahir')),
                'jenisUjian' => $request->input('jenis-ujian'),
                'gelar' => $request->input('gelar'),
                'tanggalUjian' => formatTimestampToOnlyDateIndonesian($request->input('tanggal-ujian')),
                'periodeWisuda' => $request->input('periode-wisuda'),
                'tanggalWisuda' => formatTimestampToOnlyMonthIndonesian($request->input('tanggal-wisuda')),

            ];
            if ($request->hasFile('bukti-lulus')) {
                $request->validate([
                    'bukti-lulus' => 'file|mimes:jpeg,png,jpg,pdf|max:2048',
                ]);

                try {

                    $stringStorage = $request->file('bukti-lulus')->store('lampiran');
                } catch (Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
                $surat->files = [
                    'buktiLulus' => $stringStorage,
                ];
            }


            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }

            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-keterangan-alumni') {
            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'no-ijazah' => 'required',
                'birthplace' => 'required',
                'birthdate' => 'required|date',
                'tahunAkademikAwal' => 'required|date_format:Y|digits:4',
                'tahunAkademikAkhir' => 'required|date_format:Y|digits:4',
                'email' => 'required|email',
                'ijazah' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();


            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            // $surat->penerima_id = $kaprodi->id;
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'noIjazah' => $request->input('no-ijazah'),
                'tempatLahir' => $request->input('birthplace'),
                'tanggalLahir' => formatTimestampToOnlyDateIndonesian($request->input('birthdate')),
                'tahunAkademikAwal' => $request->input('tahunAkademikAwal'),
                'tahunAkademikAkhir' => $request->input('tahunAkademikAkhir'),
                'email' => $request->input('email'),

            ];
            $surat->files = [
                'ijazah' => $request->file('ijazah')->store('lampiran')
            ];

            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }

            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-keterangan-pernah-kuliah') {
            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'tahunAkademikAwal' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
                'tahunAkademikAkhir' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
                'email' => 'required|email',
                'semester-masuk' => 'required|filled',
                'semester-selesai' => 'required|filled',
                'bukti-kuliah' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'bukti-kuliah-2' => 'file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();


            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            // $surat->penerima_id = $kaprodi->id;
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'semesterMasuk' => $request->input('semester-masuk'),
                'semesterSelesai' => $request->input('semester-selesai'),
                'tahunAkademikAwal' => $request->input('tahunAkademikAwal'),
                'tahunAkademikAkhir' => $request->input('tahunAkademikAkhir'),
                'email' => $request->input('email'),

            ];
            $surat->files = [
                'buktiKuliah' => $request->file('bukti-kuliah')->store('lampiran'),
            ];

            $files = $surat->files;
            if ($request->hasFile('bukti-kuliah-2')) {
                $files['buktiKuliah2'] = $request->file('bukti-kuliah-2')->store('lampiran');
            }
            $surat->files = $files;


            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-aktif-kuliah') {
            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'tahunAkademik' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
                'semester' => 'required|filled',
                'jenis-semester' => 'required|filled',
                'nama-orang-tua' => 'required',
                'pekerjaan-orang-tua' => 'required',
                'pangkat-orang-tua' => '',
                'nip-orang-tua' => '',
                'slip-pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'sk-orang-tua' => 'file|mimes:jpeg,png,jpg,pdf|max:2048',
                'ktm' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'krs' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();


            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            // $surat->penerima_id = $kaprodi->id;
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'semester' => $request->input('semester'),
                'jenis-semester' => $request->input('jenis-semester'),
                'namaOrangTuaAtauWali' => $request->input('nama-orang-tua'),
                'instansiAtauPekerjaanOrangTuaAtauWali' => $request->input('pekerjaan-orang-tua'),
                'pangkatAtauGolonganOrangTuaAtauWali' => $request->input('pangkat-orang-tua'),
                'nipOrangTuaAtauWali' => $request->input('nip-orang-tua'),
                'tahunAkademik' => $request->input('tahunAkademik'),

            ];
            $surat->files = [
                'slipPembayaran' => $request->file('slip-pembayaran')->store('lampiran'),
                'ktm' => $request->file('ktm')->store('lampiran'),
                'krs' => $request->file('krs')->store('lampiran'),
            ];

            $files = $surat->files;
            if ($request->hasFile('sk-orang-tua')) {
                $files['skOrangTua'] = $request->file('sk-orang-tua')->store('lampiran');
            }
            $surat->files = $files;


            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-keterangan-eligible-pin') {

            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'periode-wisuda' => 'required|numeric',
                'tanggal-wisuda' => 'required|date_format:Y-m',
                'bukti-lulus' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048'
            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'periodeWisuda' => $request->input('periode-wisuda'),
                'tanggalWisuda' => formatTimestampToOnlyMonthIndonesian($request->input('tanggal-wisuda')),

            ];
            if ($request->hasFile('bukti-lulus')) {
                $request->validate([
                    'bukti-lulus' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                ]);

                $surat->files = [
                    'buktiLulus' => $request->file('bukti-lulus')->store('lampiran')
                ];
            }

            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }

            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-permohonan-izin-penelitian-mahasiswa') {

            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'tujuan1' => 'required',
                'tujuan2' => '',
                'tujuan3' => '',
                'judul-skripsi' => 'required',
                'tempat-penelitian' => 'required',
                'waktu-mulai-penelitian' => 'required|date',
                'waktu-selesai-penelitian' => 'required|date',
                'berkas-proposal' => 'required|file|mimes:pdf|max:10240',

            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'tujuan1' => $request->input('tujuan1'),
                'tujuan2' => $request->input('tujuan2'),
                'tujuan3' => $request->input('tujuan3'),
                'judulSkripsi' => $request->input('judul-skripsi'),
                'tempatPenelitian' => $request->input('tempat-penelitian'),
                'waktuMulaiPenelitian' => formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-penelitian')),
                'waktuSelesaiPenelitian' => formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-penelitian')),

            ];
            if ($request->hasFile('berkas-proposal')) {
                $request->validate([
                    'berkas-proposal' => 'file|mimes:pdf|max:10240',
                ]);

                $surat->files = [
                    'berkasProposal' => $request->file('berkas-proposal')->store('lampiran')
                ];
            }


            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-permohonan-izin-prapenelitian-mahasiswa') {

            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'tujuan1' => 'required',
                'tujuan2' => '',
                'tujuan3' => '',
                'judul-skripsi' => 'required',
                'tempat-prapenelitian' => 'required',
                'waktu-mulai-prapenelitian' => 'required|date',
                'waktu-selesai-prapenelitian' => 'required|date',
                'berkas-proposal' => 'required|file|mimes:pdf|max:10240',

            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'tujuan1' => $request->input('tujuan1'),
                'tujuan2' => $request->input('tujuan2'),
                'tujuan3' => $request->input('tujuan3'),
                'judulSkripsi' => $request->input('judul-skripsi'),
                'tempatPraPenelitian' => $request->input('tempat-prapenelitian'),
                'waktuMulaiPraPenelitian' => formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-prapenelitian')),
                'waktuSelesaiPraPenelitian' => formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-prapenelitian')),

            ];
            if ($request->hasFile('berkas-proposal')) {
                $request->validate([
                    'berkas-proposal' => 'file|mimes:pdf|max:10240',
                ]);

                $surat->files = [
                    'berkasProposal' => $request->file('berkas-proposal')->store('lampiran')
                ];
            }


            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-keterangan-kesalahan-ijazah') {
            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'tempat-lahir' => 'required',
                'tanggal-lahir' => 'required|date',
                'tahun-angkatan' => 'required|date_format:Y|digits:4',
                'tanggal-lulus' => 'required|date',
                'jenis-kesalahan' => 'required|max:30',
                'kesalahan' => 'required|max:100',
                'kebenaran' => 'required|max:100',
                'email' => 'required|email',
                'ijazah' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();


            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            // $surat->penerima_id = $kaprodi->id;
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'tempatLahir' => $request->input('tempat-lahir'),
                'tanggalLahir' => formatTimestampToOnlyDateIndonesian($request->input('tanggal-lahir')),
                'tahunAngkatan' => $request->input('tahun-angkatan'),
                'tanggalLulus' => formatTimestampToOnlyDateIndonesian($request->input('tanggal-lulus')),
                'jenisKesalahan' => $request->input('jenis-kesalahan'),
                'dataAtauPenulisanYangSalah' => $request->input('kesalahan'),
                'dataAtauPenulisanYangBenar' => $request->input('kebenaran'),

            ];
            $surat->files = [
                'ijazah' => $request->file('ijazah')->store('lampiran')
            ];


            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-rekomendasi-mbkm') {
            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'semester' => 'required',
                'ipk' =>  ['required', 'regex:/^\d(\.\d{2})?$/'],
                'jenis-program' => 'required',
                'semester-saat-program-berlangsung' => 'required',
                'tahun-akademik-saat-program-berlangsung' =>  ['required', 'regex:/^\d{4}\/\d{4}$/'],
                'ktm' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();


            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            // $surat->penerima_id = $kaprodi->id;
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'semester' => $request->input('semester'),
                'ipk' => $request->input('ipk'),
                'jenisProgram' => $request->input('jenis-program'),
                'semesterSaatProgramBerlangsung' => $request->input('semester-saat-program-berlangsung'),
                'tahunAkademikSaatProgramBerlangsung' => $request->input('tahun-akademik-saat-program-berlangsung')

            ];
            $surat->files = [
                'ktm' => $request->file('ktm')->store('lampiran')
            ];

            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        } elseif ($jenisSurat->slug == 'surat-pengantar-pembayaran-uang-yudisium') {

            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'periode-yudisium' => 'required|numeric',
                'tanggal-yudisium' => 'required|date_format:Y-m',
                'formulir-biodata' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'bebas-fakultas' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'ttd-sumbangan' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'uji-plagiarisme' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'sertifikat-kompetensi' => 'file|mimes:jpeg,png,jpg,pdf|max:2048',
                'bukti-pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',

            ]);

            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            $surat->data = [
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'periodeYudisium' => $request->input('periode-yudisium'),
                'tanggalYudisium' => formatTimestampToOnlyMonthIndonesian($request->input('tanggal-yudisium')),

            ];

            $surat->files = [
                'formulirBiodata' => $request->file('formulir-biodata')->store('lampiran'),
                'bebasFakultas' => $request->file('bebas-fakultas')->store('lampiran'),
                'ttdSumbangan' => $request->file('ttd-sumbangan')->store('lampiran'),
                'ujiPlagiarisme' => $request->file('uji-plagiarisme')->store('lampiran'),
                'buktiPembayaran' => $request->file('bukti-pembayaran')->store('lampiran'),
            ];

            $files = $surat->files;
            if ($request->hasFile('sertifikat-kompetensi')) {
                $files['sertifikatKompetensi'] = $request->file('sertifikat-kompetensi')->store('lampiran');
            }
            $surat->files = $files;


            if (
                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where('status', 'diproses')
                ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
                ->count() > 0
            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
            }
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        }
    }

    public function storeByStaffDekan(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'surat-keluar') {


            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'email' => 'required|email',
                'perihal' => 'required',
                'jumlah-lampiran' => 'required|integer',
                'lampiran' => 'file|mimes:jpeg,png,jpg,pdf|max:10240',
                'tujuan1' => 'required',
                'tujuan2' => 'nullable',
                'tujuan3' => 'nullable',
                'paragraf-awal' => 'required',
                'tanggal-mulai-kegiatan' => '',
                'waktu' => 'nullable',
                'tempat' => 'nullable',
                'tembusan' => 'nullable|array',
                'tanggal-terbit-otomatis' => 'boolean',
                'paragraf-akhir' => 'required',
            ]);


            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = $request->input('penerima');
            $surat->status = 'diproses';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(30);
            // $surat->data = [
            //     'private' => [
            //         'stepper' => [auth()->user()->role->id],
            //         'tanggalMulaiKegiatan' => $request->input('tanggal-mulai-kegiatan') ?? '',
            //         'tanggalSelesaiKegiatan' => $request->input('tanggal-selesai-kegiatan') ?? '',
            //         'waktuMulaiKegiatan' => $request->input('waktu') ?? '',
            //         'waktuSelesaiKegiatan' => $request->input('waktu-selesai') ?? ''

            //     ],
            //     'nama' => $request->input('name'),
            //     'username' => $request->input('username'),
            //     'email' => $request->input('email'),
            //     'perihal' => $request->input('perihal'),
            //     'jumlahLampiran' => $request->input('jumlah-lampiran'),
            //     'tujuan1' => $request->input('tujuan1'),
            //     'tujuan2' => $request->input('tujuan2'),
            //     'tujuan3' => $request->input('tujuan3'),
            //     'paragrafAwal' => $request->input('paragraf-awal'),
            //     'tanggalPelaksanaan' => $request->has('tanggal-selesai-kegiatan') && !empty($request->input('tanggal-selesai-kegiatan')) ? formatTimestampToDayIndonesian($request->input('tanggal-mulai-kegiatan')) . ' .s.d. ' . formatTimestampToDayIndonesian($request->input('tanggal-selesai-kegiatan')) . ', ' . formatTimestampToOnlyDateIndonesian($request->input('tanggal-mulai-kegiatan')) . ' .s.d. ' . formatTimestampToOnlyDateIndonesian($request->input('tanggal-selesai-kegiatan')) : formatTimestampToDateIndonesian($request->input('tanggal-mulai-kegiatan')),
            //     'waktu' => $request->input('waktu') == 'Jadwal terlampir'
            //         ? $request->input('waktu')
            //         : ($request->has('waktu-selesai') && !empty($request->input('waktu-selesai'))
            //             ? $request->input('waktu') . ' WIB .s.d. ' . $request->input('waktu-selesai') . ' WIB'
            //             : $request->input('waktu') . ' WIB' . ' s.d. ' . 'selesai'),

            //     'tempat' => $request->input('tempat'),
            //     'paragrafAkhir' => $request->input('paragraf-akhir'),


            // ];
            $surat->data = [
                'private' => [
                    'stepper' => [auth()->user()->role->id],
                    'tanggalMulaiKegiatan' => $request->input('tanggal-mulai-kegiatan') ?? null,
                    'tanggalSelesaiKegiatan' => $request->input('tanggal-selesai-kegiatan') ?? null,
                    'tanggalTerbitOtomatis' => $request->input('tanggal-terbit-otomatis') ? true : false,
                    'tembusan' => $request->input('tembusan') == null || $request->input('tembusan')[0] == null ? null : $request->input('tembusan'),
                ],
                'nama' => $request->input('name'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'perihal' => $request->input('perihal'),
                'jumlahLampiran' => $request->input('jumlah-lampiran'),
                'tujuan1' => $request->input('tujuan1'),
                'tujuan2' => $request->input('tujuan2'),
                'tujuan3' => $request->input('tujuan3'),
                'paragrafAwal' => $request->input('paragraf-awal'),

                // Conditional assignment for tanggalPelaksanaan
                'tanggalPelaksanaan' => ($request->input('tanggal-mulai-kegiatan') && $request->input('tanggal-selesai-kegiatan'))
                    ? formatTimestampToDayIndonesian($request->input('tanggal-mulai-kegiatan')) . ' s.d. ' . formatTimestampToDayIndonesian($request->input('tanggal-selesai-kegiatan')) . ', ' . formatTimestampToOnlyDateIndonesian($request->input('tanggal-mulai-kegiatan')) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($request->input('tanggal-selesai-kegiatan'))
                    : (($request->input('tanggal-mulai-kegiatan'))
                        ? formatTimestampToDateIndonesian($request->input('tanggal-mulai-kegiatan'))
                        : null),

                'waktu' => ($request->input('waktu') !== '') ? $request->input('waktu') : null,
                // Conditional assignment for tempat
                'tempat' => ($request->input('tempat') !== '') ? $request->input('tempat') : null,
                'paragrafAkhir' => $request->input('paragraf-akhir'),
                'tembusan' => $request->input('tembusan') == null || $request->input('tembusan')[0] == null ? '-' :  implode(', ', array_filter($request->input('tembusan'))),
            ];




            if ($request->hasFile('lampiran')) {
                $surat->files = [
                    'lampiran' => $request->file('lampiran')->store('lampiran')
                ];
            }


            $surat->save();
            return redirect('/staff-dekan/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        }
    }

    public function storeSuratTugasByStaff(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-tugas') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }


        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'program-studi' => 'required',
            'email' => 'required|email',
            'nama-dosen' => 'required',
            'nip-dosen' => 'required',
            'pangkat-dosen' => 'required',
            'jabatan-fungsional-dosen' => 'required',
            'acara' => 'required',
            'tempat' => 'required',
            'waktu-mulai-penugasan' => 'required|date',
            'waktu-selesai-penugasan' => 'required|date',
            'dasar-penugasan' => 'required',
            'lampiran' => 'file|mimes:jpeg,png,jpg,pdf|max:10240',

        ]);

        $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'private' => [
                'stepper' => [3],
                'waktuMulaiPenugasan' => $request->input('waktu-mulai-penugasan'),
                'waktuSelesaiPenugasan' => $request->input('waktu-selesai-penugasan'),
            ],
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'programStudi' => $programStudi->name,
            'email' => $request->input('email'),
            'dosen' => [
                [

                    'namaDosen' => $request->input('nama-dosen'),
                    'nipDosen' => $request->input('nip-dosen'),
                    'pangkatDosen' => $request->input('pangkat-dosen'),
                    'jabatanFungsionalDosen' => $request->input('jabatan-fungsional-dosen'),
                ]
            ],
            // 'namaDosen' => $request->input('nama-dosen'),
            // 'nipDosen' => $request->input('nip-dosen'),
            // 'pangkatDosen' => $request->input('pangkat-dosen'),
            // 'jabatanFungsionalDosen' => $request->input('jabatan-fungsional-dosen'),
            'acara' => $request->input('acara'),
            'tempat' => $request->input('tempat'),
            'waktuPelaksanaan' => formatTimestampToDayIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToDayIndonesian($request->input('waktu-selesai-penugasan')) . ', ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-penugasan')),
            'dasarPenugasan' => $request->input('dasar-penugasan'),
        ];
        if ($request->hasFile('lampiran')) {
            $request->validate([
                'lampiran' => 'file|mimes:jpeg,png,jpg,pdf|max:10240',
            ]);

            $surat->files = [
                'lampiran' => $request->file('lampiran')->store('lampiran')
            ];
        }

        $surat->save();
        return redirect('/staff/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeSuratTugasKelompokByStaff(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-tugas-kelompok') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'program-studi' => 'required',
            'email' => 'required|email',
            'nama-dosen.*' => 'required',
            'nip-dosen.*' => 'required',
            'jabatan-dosen.*' => 'required',
            'acara' => 'required',
            'tempat' => 'required',
            'waktu-mulai-penugasan' => 'required|date',
            'waktu-selesai-penugasan' => 'required|date',
            'lampiran' => 'file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

        $dosen = [];
        $namaDosenArray = $request->input('nama-dosen');
        $nipDosenArray = $request->input('nip-dosen');
        $jabatanDosenArray = $request->input('jabatan-dosen');
        for ($i = 0; $i < count($namaDosenArray); $i++) {
            $dosen[] = [
                'namaDosen' . $i + 1 => $namaDosenArray[$i],
                'nipDosen' . $i + 1 => $nipDosenArray[$i],
                'jabatanDosen' . $i + 1 => $jabatanDosenArray[$i],
            ];
        }

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'private' => [
                'stepper' => [auth()->user()->role->id],
                'waktuMulaiPenugasan' => $request->input('waktu-mulai-penugasan'),
                'waktuSelesaiPenugasan' => $request->input('waktu-selesai-penugasan'),
            ],
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'programStudi' => $programStudi->name,
            'email' => $request->input('email'),
            'dosen' => $dosen,
            'acara' => $request->input('acara'),
            'tempat' => $request->input('tempat'),
            'waktuPelaksanaan' => formatTimestampToDayIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToDayIndonesian($request->input('waktu-selesai-penugasan')) . ', ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-penugasan')),
        ];

        if ($request->hasFile('lampiran')) {
            $surat->files = [
                'lampiran' => $request->file('lampiran')->store('lampiran')
            ];
        }

        $surat->save();
        return redirect('/staff/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeBeritaAcaraNilaiByStaff(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'berita-acara-nilai') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'program-studi' => 'required',
            'email' => 'required|email',
            'berita-acara-nilai' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);

        $surat->data = [
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'programStudi' => auth()->user()->programStudi->name,
            'email' => $request->input('email'),
        ];

        $surat->files = [
            'beritaAcaraNilai' => $request->file('berita-acara-nilai')->store('lampiran'),
        ];

        $surat->save();
        return redirect('/staff/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }


    public function storeSuratTugasByStaffDekan(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-tugas-from-staff-dekan') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }


        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'nip-dosen' => 'required',
            'nama-dosen' => 'required',
            'pangkat-dosen' => 'required',
            'jabatan-fungsional-dosen' => 'required',
            'acara' => 'required',
            'tempat' => 'required',
            'waktu-mulai-penugasan' => 'required|date',
            'waktu-selesai-penugasan' => 'required|date',
            'dasar-penugasan' => 'required',
            'lampiran' => 'file|mimes:jpeg,png,jpg,pdf|max:10240',

        ]);

        $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'private' => [
                'stepper' => [auth()->user()->role->id],
                'waktuMulaiPenugasan' => $request->input('waktu-mulai-penugasan'),
                'waktuSelesaiPenugasan' => $request->input('waktu-selesai-penugasan'),
            ],
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'dosen' => [
                [

                    'namaDosen' => $request->input('nama-dosen'),
                    'nipDosen' => $request->input('nip-dosen'),
                    'pangkatDosen' => $request->input('pangkat-dosen'),
                    'jabatanFungsionalDosen' => $request->input('jabatan-fungsional-dosen'),
                ]
            ],
            // 'namaDosen' => $request->input('nama-dosen'),
            // 'nipDosen' => $request->input('nip-dosen'),
            // 'pangkatDosen' => $request->input('pangkat-dosen'),
            // 'jabatanFungsionalDosen' => $request->input('jabatan-fungsional-dosen'),
            'acara' => $request->input('acara'),
            'tempat' => $request->input('tempat'),
            'waktuPelaksanaan' => formatTimestampToDayIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToDayIndonesian($request->input('waktu-selesai-penugasan')) . ', ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-penugasan')),
            'dasarPenugasan' => $request->input('dasar-penugasan'),
        ];
        if ($request->hasFile('lampiran')) {
            $request->validate([
                'lampiran' => 'file|mimes:jpeg,png,jpg,pdf|max:10240',
            ]);

            $surat->files = [
                'lampiran' => $request->file('lampiran')->store('lampiran')
            ];
        }

        $surat->save();
        return redirect('/staff-dekan/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeSuratTugasKelompokByStaffDekan(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-tugas-kelompok-from-staff-dekan') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'nama-dosen.*' => 'required',
            'nip-dosen.*' => 'required',
            'jabatan-dosen.*' => 'required',
            'acara' => 'required',
            'tempat' => 'required',
            'waktu-mulai-penugasan' => 'required|date',
            'waktu-selesai-penugasan' => 'required|date',
            'lampiran' => 'file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

        $dosen = [];
        $namaDosenArray = $request->input('nama-dosen');
        $nipDosenArray = $request->input('nip-dosen');
        $jabatanDosenArray = $request->input('jabatan-dosen');
        for ($i = 0; $i < count($namaDosenArray); $i++) {
            $dosen[] = [
                'namaDosen' . $i + 1 => $namaDosenArray[$i],
                'nipDosen' . $i + 1 => $nipDosenArray[$i],
                'jabatanDosen' . $i + 1 => $jabatanDosenArray[$i],
            ];
        }

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'private' => [
                'stepper' => [auth()->user()->role->id],
                'waktuMulaiPenugasan' => $request->input('waktu-mulai-penugasan'),
                'waktuSelesaiPenugasan' => $request->input('waktu-selesai-penugasan'),
            ],
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'dosen' => $dosen,
            'acara' => $request->input('acara'),
            'tempat' => $request->input('tempat'),
            'waktuPelaksanaan' => formatTimestampToDayIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToDayIndonesian($request->input('waktu-selesai-penugasan')) . ', ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-penugasan')) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-penugasan')),
        ];

        if ($request->hasFile('lampiran')) {
            $surat->files = [
                'lampiran' => $request->file('lampiran')->store('lampiran')
            ];
        }

        $surat->save();
        return redirect('/staff-dekan/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeSuratPengajuanAtkByStaff(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-pengajuan-atk') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'program-studi' => 'required',
            'email' => 'required|email',
            'pengajuan-atk' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);

        $surat->data = [
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'programStudi' => auth()->user()->programStudi->name,
            'email' => $request->input('email'),
        ];

        $surat->files = [
            'pengajuanAtk' => $request->file('pengajuan-atk')->store('lampiran'),
        ];

        $surat->save();
        return redirect('/staff/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeSuratPengajuanAtkByAkademik(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-pengajuan-atk-akademik') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'pengajuan-atk' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        // Langsung ke Kabag (role_id 17)
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);

        $surat->data = [
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ];

        $surat->files = [
            'pengajuanAtk' => $request->file('pengajuan-atk')->store('lampiran'),
        ];

        $surat->save();
        return redirect('/akademik/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeByAkademik(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'surat-pengajuan-atk-akademik') {
            return $this->storeSuratPengajuanAtkByAkademik($request, $jenisSurat);
        }

        return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
    }

    public function storeSuratPengajuanAtkByAkademikFakultas(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-pengajuan-atk-akademik-fakultas') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'pengajuan-atk' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        // Langsung ke Kabag (role_id 17)
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);

        $surat->data = [
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ];

        $surat->files = [
            'pengajuanAtk' => $request->file('pengajuan-atk')->store('lampiran'),
        ];

        $surat->save();
        return redirect('/akademik-fakultas/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeByAkademikFakultas(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'surat-pengajuan-atk-akademik-fakultas') {
            return $this->storeSuratPengajuanAtkByAkademikFakultas($request, $jenisSurat);
        }

        return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
    }

    public function storeSuratPengajuanAtkByKemahasiswaan(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-pengajuan-atk-kemahasiswaan') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }

        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'pengajuan-atk' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        // Langsung ke Kabag (role_id 17)
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);

        $surat->data = [
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ];

        $surat->files = [
            'pengajuanAtk' => $request->file('pengajuan-atk')->store('lampiran'),
        ];

        $surat->save();
        return redirect('/kemahasiswaan/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeByKemahasiswaan(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'surat-pengajuan-atk-kemahasiswaan') {
            return $this->storeSuratPengajuanAtkByKemahasiswaan($request, $jenisSurat);
        }

        return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
    }

    public function storeSuratPengajuanAtkByTataUsaha(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-pengajuan-atk-tata-usaha') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'pengajuan-atk' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);
        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        // Langsung ke Kabag (role_id 17)
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ];
        $surat->files = [
            'pengajuanAtk' => $request->file('pengajuan-atk')->store('lampiran'),
        ];
        $surat->save();
        return redirect('/tata-usaha/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeByTataUsaha(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'surat-pengajuan-atk-tata-usaha') {
            return $this->storeSuratPengajuanAtkByTataUsaha($request, $jenisSurat);
        }
        return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
    }

    public function storeSuratPengajuanAtkByUnitKerjasama(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug != 'surat-pengajuan-atk-unit-kerjasama') {
            return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
        }
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'pengajuan-atk' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);
        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        // Langsung ke Kabag (role_id 17)
        $surat->current_user_id = $request->input('penerima');
        $surat->status = 'diproses';
        $surat->jenis_surat_id = $jenisSurat->id;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'nama' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
        ];
        $surat->files = [
            'pengajuanAtk' => $request->file('pengajuan-atk')->store('lampiran'),
        ];
        $surat->save();
        return redirect('/unit-kerjasama/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }

    public function storeByUnitKerjasama(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'surat-pengajuan-atk-unit-kerjasama') {
            return $this->storeSuratPengajuanAtkByUnitKerjasama($request, $jenisSurat);
        }
        return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
    }

    public function destroy(Surat $surat)
    {
        if ($surat->status == 'diproses' || $surat->status == 'menunggu_pembayaran') {
            Surat::destroy($surat->id);
            return redirect()->back()->with('success', 'Berhasil membatalkan pengajuan surat');
        }
        return redirect()->back()->with('deleted', 'Gagal membatalkan pengajuan surat');
    }

    public function edit(Surat $surat)
    {
        if ($surat->jenisSurat->slug == 'surat-tugas') {
            $viewPrefix = auth()->user()->role->name;
            $viewName = "{$viewPrefix}.formsurat.edit-form-surat-tugas";
            // dd($viewName);
            return view($viewName, [
                'surat' => $surat,
            ]);
        }
        if ($surat->jenisSurat->slug == 'surat-tugas-kelompok') {
            $viewPrefix = auth()->user()->role->name;
            $viewName = "{$viewPrefix}.formsurat.edit-form-surat-tugas-kelompok";
            // dd($viewName);
            return view($viewName, [
                'surat' => $surat,
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-tugas-from-staff-dekan') {
            $viewPrefix = auth()->user()->role->name;
            $viewName = "{$viewPrefix}.formsurat.edit-form-surat-tugas-from-staff-dekan";
            // dd($viewName);
            return view($viewName, [
                'surat' => $surat,
            ]);
        }
        if ($surat->jenisSurat->slug == 'surat-tugas-kelompok-from-staff-dekan') {
            $viewPrefix = auth()->user()->role->name;
            $viewName = "{$viewPrefix}.formsurat.edit-form-surat-tugas-kelompok-from-staff-dekan";
            // dd($viewName);
            return view($viewName, [
                'surat' => $surat,
            ]);
        }

        if ($surat->jenisSurat->slug == 'surat-keluar') {
            $viewPrefix = auth()->user()->role->name;
            $viewName = "{$viewPrefix}.formsurat.edit-form-surat-keluar";
            // dd($viewName);
            return view($viewName, [
                'surat' => $surat,
            ]);
        }
        return redirect()->back()->with('deleted', 'Jenis Surat ini tidak dapat diedit');
    }

    public function update(Request $request, Surat $surat)
    {

        if ($surat->jenisSurat->slug == 'surat-tugas') {
            // dd($request->all());
            $newData = $request->validate([
                'nama-dosen' => 'required',
                'nip-dosen' => 'required',
                'pangkat-dosen' => 'required',
                'jabatan-fungsional-dosen' => 'required',
                'acara' => 'required',
                'tempat' => 'required',
                'waktu-mulai-penugasan' => 'required|date',
                'waktu-selesai-penugasan' => 'required|date',
                'dasar-penugasan' => 'required',

            ]);


            $updatedSurat = Surat::find($surat->id);

            // Dekode data JSON
            $data = $surat->data;

            // Perbarui atribut yang diinginkan dalam array data
            $data['acara'] = $newData['acara'];
            $data['tempat'] = $newData['tempat'];
            $data['dasarPenugasan'] = $newData['dasar-penugasan'];
            $data['private']['waktuMulaiPenugasan'] = $newData['waktu-mulai-penugasan'];
            $data['private']['waktuSelesaiPenugasan'] = $newData['waktu-selesai-penugasan'];
            $data['waktuPelaksanaan'] = formatTimestampToDayIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToDayIndonesian($newData['waktu-selesai-penugasan']) . ', ' . formatTimestampToOnlyDateIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($newData['waktu-selesai-penugasan']);

            $data['dosen'] = [
                [

                    'namaDosen' => $request->input('nama-dosen'),
                    'nipDosen' => $request->input('nip-dosen'),
                    'pangkatDosen' => $request->input('pangkat-dosen'),
                    'jabatanFungsionalDosen' => $request->input('jabatan-fungsional-dosen'),
                ]
            ];



            // Encode kembali data menjadi JSON
            $updatedSurat->data = $data;

            // Simpan data yang diperbarui ke database
            $updatedSurat->save();
        }

        if ($surat->jenisSurat->slug == 'surat-tugas-kelompok') {
            // dd($request->all());
            $newData = $request->validate([
                'acara' => 'required',
                'tempat' => 'required',
                'waktu-mulai-penugasan' => 'required|date',
                'waktu-selesai-penugasan' => 'required|date',

            ]);


            $updatedSurat = Surat::find($surat->id);

            // Dekode data JSON
            $data = $surat->data;

            // Perbarui atribut yang diinginkan dalam array data
            $data['acara'] = $newData['acara'];
            $data['tempat'] = $newData['tempat'];
            $data['private']['waktuMulaiPenugasan'] = $newData['waktu-mulai-penugasan'];
            $data['private']['waktuSelesaiPenugasan'] = $newData['waktu-selesai-penugasan'];
            $data['waktuPelaksanaan'] = formatTimestampToDayIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToDayIndonesian($newData['waktu-selesai-penugasan']) . ', ' . formatTimestampToOnlyDateIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($newData['waktu-selesai-penugasan']);

            // Perbarui data dosen
            // foreach ($newData['nama-dosen'] as $index => $namaDosen) {
            //     $data['dosen'][$index]['namaDosen' . ($index + 1)] = $namaDosen;
            //     $data['dosen'][$index]['nipDosen' . ($index + 1)] = $newData['nip-dosen'][$index];
            //     $data['dosen'][$index]['jabatanDosen' . ($index + 1)] = $newData['jabatan-dosen'][$index];
            // }

            $dosen = [];
            $index = 1;
            while ($request->has("namaDosen{$index}")) {
                $dosen[] = [
                    "namaDosen{$index}" => $request->input("namaDosen{$index}"),
                    "nipDosen{$index}" => $request->input("nipDosen{$index}"),
                    "jabatanDosen{$index}" => $request->input("jabatanDosen{$index}"),
                ];
                $index++;
            }
            $data['dosen'] = $dosen;


            // Encode kembali data menjadi JSON
            $updatedSurat->data = $data;

            // Simpan data yang diperbarui ke database
            $updatedSurat->save();
        }


        if ($surat->jenisSurat->slug == 'surat-tugas-from-staff-dekan') {
            // dd($request->all());
            $newData = $request->validate([
                'nama-dosen' => 'required',
                'nip-dosen' => 'required',
                'pangkat-dosen' => 'required',
                'jabatan-fungsional-dosen' => 'required',
                'acara' => 'required',
                'tempat' => 'required',
                'waktu-mulai-penugasan' => 'required|date',
                'waktu-selesai-penugasan' => 'required|date',
                'dasar-penugasan' => 'required',

            ]);


            $updatedSurat = Surat::find($surat->id);

            // Dekode data JSON
            $data = $surat->data;

            // Perbarui atribut yang diinginkan dalam array data
            $data['acara'] = $newData['acara'];
            $data['tempat'] = $newData['tempat'];
            $data['dasarPenugasan'] = $newData['dasar-penugasan'];
            $data['private']['waktuMulaiPenugasan'] = $newData['waktu-mulai-penugasan'];
            $data['private']['waktuSelesaiPenugasan'] = $newData['waktu-selesai-penugasan'];
            $data['waktuPelaksanaan'] = formatTimestampToDayIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToDayIndonesian($newData['waktu-selesai-penugasan']) . ', ' . formatTimestampToOnlyDateIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($newData['waktu-selesai-penugasan']);

            $data['dosen'] = [
                [

                    'namaDosen' => $request->input('nama-dosen'),
                    'nipDosen' => $request->input('nip-dosen'),
                    'pangkatDosen' => $request->input('pangkat-dosen'),
                    'jabatanFungsionalDosen' => $request->input('jabatan-fungsional-dosen'),
                ]
            ];



            // Encode kembali data menjadi JSON
            $updatedSurat->data = $data;

            // Simpan data yang diperbarui ke database
            $updatedSurat->save();
        }

        if ($surat->jenisSurat->slug == 'surat-tugas-kelompok-from-staff-dekan') {
            // dd($request->all());
            $newData = $request->validate([
                'acara' => 'required',
                'tempat' => 'required',
                'waktu-mulai-penugasan' => 'required|date',
                'waktu-selesai-penugasan' => 'required|date',

            ]);


            $updatedSurat = Surat::find($surat->id);

            // Dekode data JSON
            $data = $surat->data;

            // Perbarui atribut yang diinginkan dalam array data
            $data['acara'] = $newData['acara'];
            $data['tempat'] = $newData['tempat'];
            $data['private']['waktuMulaiPenugasan'] = $newData['waktu-mulai-penugasan'];
            $data['private']['waktuSelesaiPenugasan'] = $newData['waktu-selesai-penugasan'];
            $data['waktuPelaksanaan'] = formatTimestampToDayIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToDayIndonesian($newData['waktu-selesai-penugasan']) . ', ' . formatTimestampToOnlyDateIndonesian($newData['waktu-mulai-penugasan']) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($newData['waktu-selesai-penugasan']);

            // Perbarui data dosen
            // foreach ($newData['nama-dosen'] as $index => $namaDosen) {
            //     $data['dosen'][$index]['namaDosen' . ($index + 1)] = $namaDosen;
            //     $data['dosen'][$index]['nipDosen' . ($index + 1)] = $newData['nip-dosen'][$index];
            //     $data['dosen'][$index]['jabatanDosen' . ($index + 1)] = $newData['jabatan-dosen'][$index];
            // }

            $dosen = [];
            $index = 1;
            while ($request->has("namaDosen{$index}")) {
                $dosen[] = [
                    "namaDosen{$index}" => $request->input("namaDosen{$index}"),
                    "nipDosen{$index}" => $request->input("nipDosen{$index}"),
                    "jabatanDosen{$index}" => $request->input("jabatanDosen{$index}"),
                ];
                $index++;
            }
            $data['dosen'] = $dosen;


            // Encode kembali data menjadi JSON
            $updatedSurat->data = $data;

            // Simpan data yang diperbarui ke database
            $updatedSurat->save();
        }

        if ($surat->jenisSurat->slug == 'surat-keluar') {
            // dd($request->all());


            $newData = $request->validate([
                'perihal' => 'required',
                'jumlah-lampiran' => 'required|integer',
                'tujuan1' => 'required',
                'tujuan2' => 'nullable|string', // Use 'nullable' instead of '' for optional fields
                'tujuan3' => 'nullable|string',
                'paragraf-awal' => 'required|string',
                'tembusan' => 'nullable|array',
                'tanggal-mulai-kegiatan' => 'nullable',
                'waktu' => 'nullable', // Ensure 'waktu' is in HH:MM format
                'tempat' => 'nullable',
                'paragraf-akhir' => 'required|string',
            ]);



            $updatedSurat = Surat::find($surat->id);

            // Dekode data JSON
            $data = $surat->data;

            // Perbarui atribut yang diinginkan dalam array data
            $data['perihal'] = $newData['perihal'];
            $data['jumlahLampiran'] = $newData['jumlah-lampiran'];
            $data['tujuan1'] = $newData['tujuan1'];
            $data['tujuan2'] = $newData['tujuan2'];
            $data['tujuan3'] = $newData['tujuan3'];
            $data['paragrafAwal'] = $newData['paragraf-awal'];
            $data['private']['tanggalMulaiKegiatan'] = isset($newData['tanggal-mulai-kegiatan']) ? $newData['tanggal-mulai-kegiatan'] : null;
            $data['private']['tembusan'] = $request->input('tembusan') == null || $request->input('tembusan')[0] == null ? null :  $request->input('tembusan');
            $data['tembusan'] = $request->input('tembusan') == null || $request->input('tembusan')[0] == null ? '-' :  implode(', ', array_filter($request->input('tembusan')));

            // Validate `tanggal-selesai-kegiatan` if it exists
            if (!empty($request->input('tanggal-selesai-kegiatan') && $request->has('tanggal-selesai-kegiatan') && !empty($request->input('tanggal-mulai-kegiatan') && $request->has('tanggal-mulai-kegiatan')))) {
                $request->validate([
                    'tanggal-selesai-kegiatan' => 'date|after_or_equal:tanggal-mulai-kegiatan', // Ensure it is a date and after the start date
                ]);
                $newData['tanggal-selesai-kegiatan'] = $request->input('tanggal-selesai-kegiatan');
                $data['private']['tanggalSelesaiKegiatan'] = $newData['tanggal-selesai-kegiatan'];
                $data['tanggalPelaksanaan'] = formatTimestampToDayIndonesian($newData['tanggal-mulai-kegiatan']) . ' s.d. ' . formatTimestampToDayIndonesian($newData['tanggal-selesai-kegiatan']) . ', ' . formatTimestampToOnlyDateIndonesian($newData['tanggal-mulai-kegiatan']) . ' s.d. ' . formatTimestampToOnlyDateIndonesian($newData['tanggal-selesai-kegiatan']);
            } elseif (empty($request->input('tanggal-mulai-kegiatan'))) {
                $data['tanggalPelaksanaan'] = null;
            } else {
                $data['tanggalPelaksanaan'] = formatTimestampToDateIndonesian(isset($newData['tanggal-mulai-kegiatan']) ? $newData['tanggal-mulai-kegiatan'] : null);
            }




            // Validate `waktu-selesai` if it exists
            if ($request->input('waktu') == "Jadwal terlampir") {
                $data['waktu'] = $newData['waktu'];
                $data['private']['waktuMulaiKegiatan'] = 'Jadwal terlampir';
                $data['private']['waktuSelesaiKegiatan'] = '';
            } elseif (!empty($request->input('waktu-selesai')) && $request->has('waktu-selesai')) {
                $request->validate([
                    'waktu-selesai' => 'date_format:H:i|after:waktu', // Pastikan format HH:MM dan setelah waktu mulai
                ]);
                $newData['waktu-selesai'] = $request->input('waktu-selesai');
                $data['private']['waktuMulaiKegiatan'] = $newData['waktu'] ?? null;
                $data['private']['waktuSelesaiKegiatan'] = $newData['waktu-selesai'];
                $data['waktu'] = $newData['waktu']  . ' WIB' . ' s.d. ' . $newData['waktu-selesai'] . ' WIB'; // Menambahkan WIB di akhir
            } else {
                $data['waktu'] = isset($newData['waktu']) ? $newData['waktu'] . ' WIB' . ' s.d. ' . 'selesai' : null; // Menambahkan WIB di akhir jika tidak ada waktu selesai
                $data['private']['waktuMulaiKegiatan'] = $newData['waktu'] ?? null;
                $data['private']['waktuSelesaiKegiatan'] = '';
            }


            $data['tempat'] = $newData['tempat'] ?? null;
            $data['paragrafAkhir'] = $newData['paragraf-akhir'];


            // Encode kembali data menjadi JSON
            $updatedSurat->data = $data;

            // Simpan data yang diperbarui ke database
            $updatedSurat->save();
        }

        $routePrefix = auth()->user()->role->name;
        $routeName = "show-surat-{$routePrefix}";
        return redirect(route($routeName, $surat->id))->with('success', 'Berhasil memperbarui surat');
    }

    public function previewSuratQR(Surat $surat)
    {
        return view('previews.show-surat-qr', [
            'surat' => $surat
        ]);
    }
}
