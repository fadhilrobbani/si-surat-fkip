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
                    ->where('role_id', '=', 5)
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-tugas') {
            return view('staff.formsurat.form-surat-tugas', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [8, 5, 9, 10])
                    ->orderBy('name', 'asc')
                    ->get()
            ]);
        }

        if ($jenisSurat->slug == 'surat-tugas-kelompok') {
            return view('staff.formsurat.form-surat-tugas-kelompok', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->whereIn('role_id', [8, 5, 9, 10])
                    ->orderBy('name', 'asc')
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

            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        }
    }

    public function storeByStaff(Request $request, JenisSurat $jenisSurat)
    {
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
            'waktuPelaksanaan' => formatTimestampToDayIndonesian($request->input('waktu-mulai-penugasan')) . ' .s.d. ' . formatTimestampToDayIndonesian($request->input('waktu-selesai-penugasan')) . ', ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-penugasan')) . ' .s.d. ' . formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-penugasan')),
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

    public function destroy(Surat $surat)
    {
        if ($surat->status == 'diproses') {
            Surat::destroy($surat->id);
            return redirect()->back()->with('success', 'Berhasil membatalkan pengajuan surat');
        }
        return redirect()->back()->with('deleted', 'Gagal membatalkan pengajuan surat');
    }

    public function previewSuratQR(Surat $surat)
    {
        return view('previews.show-surat-qr', [
            'surat' => $surat
        ]);
    }
}
