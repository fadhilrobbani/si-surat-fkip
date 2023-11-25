<?php

namespace App\Http\Controllers;

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

                $surat->files = [
                    'buktiLulus' => $request->file('bukti-lulus')->store('lampiran')
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
                'tahunAkademikAwal' => 'required|date_format:Y',
                'tahunAkademikAkhir' => 'required|date_format:Y',
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
                    'bukti-lulus' => 'file|mimes:jpeg,png,jpg,pdf|max:2048',
                ]);

                $surat->files = [
                    'buktiLulus' => $request->file('bukti-lulus')->store('lampiran')
                ];
            }

            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
        }
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
