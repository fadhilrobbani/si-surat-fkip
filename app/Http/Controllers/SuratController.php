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
        if ($jenisSurat->id == 6) {
            return view('mahasiswa.formsurat.form-keterangan-alumni', [
                'jenisSurat' => $jenisSurat,
                'daftarProgramStudi' => ProgramStudi::all(),
                'daftarPenerima' => User::select('id', 'name', 'username')
                    ->where('role_id', '=', 3)
                    ->where('program_studi_id', '=', auth()->user()->program_studi_id)
                    ->get()

            ]);
        }

        if ($jenisSurat->id == 8) {
            return view('mahasiswa.formsurat.form-keterangan-lulus', [
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

    public function storeSuratKeteranganAlumni(Request $request)
    {

        $pathSegments = explode('/', request()->path());
        $jenisSurat = end($pathSegments);
        // dd($jenisSurat);


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

        // $staff = DB::select('SELECT*FROM users WHERE role_id =? AND program_studi_id  = ?', [3,3]);
        // $staff = User::select('id')
        //     ->where('role_id', '=', 3)
        //     ->where('program_studi_id', '=', auth()->user()->program_studi_id)
        //     ->first();
        // dd($staff);

        $kaprodi = User::select('id')
            ->where('role_id', '=', 4)
            ->where('program_studi_id', '=',  auth()->user()->program_studi_id)
            ->first();
        // $surat = [
        //     'pengaju_id' => auth()->user()->id,
        //     'current_user_id' => $staff->id,
        //     'penerima_id' => $kaprodi->id,
        //     'status' => 'on_process',
        //     'jenis_surat_id' => $jenisSurat,
        //     'data' => [
        //         'name' => $request->input('name'),
        //         'username' => $request->input('name'),
        //         'programStudi' => $request->input('program-studi'),
        //         'noIjazah' => $request->input('no-ijazah'),
        //         'birthplace' => $request->input('birthplace'),
        //         'birthdate' => $request->input('birthdate'),
        //         'email' => $request->input('email'),
        //     ],
        // ];

        // Surat::create($surat);
        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        // $surat->penerima_id = $kaprodi->id;
        $surat->status = 'on_process';
        $surat->jenis_surat_id = $jenisSurat;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'name' => $request->input('name'),
            'username' => $request->input('name'),
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
    }

    public function storeSuratKeteranganLulus(Request $request)
    {

        $pathSegments = explode('/', request()->path());
        $jenisSurat = end($pathSegments);
        // dd($jenisSurat);


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

        // $staff = DB::select('SELECT*FROM users WHERE role_id =? AND program_studi_id  = ?', [3,3]);
        $staff = User::select('id')
            ->where('role_id', '=', 3)
            ->where('program_studi_id', '=', auth()->user()->program_studi_id)
            ->first();
        // dd($staff);

        $kaprodi = User::select('id')
            ->where('role_id', '=', 4)
            ->where('program_studi_id', '=',  auth()->user()->program_studi_id)
            ->first();

        $surat = new Surat;
        $surat->pengaju_id = auth()->user()->id;
        $surat->current_user_id = $request->input('penerima');
        // $surat->penerima_id = $kaprodi->id;
        $surat->status = 'on_process';
        $surat->jenis_surat_id = $jenisSurat;
        $surat->expired_at = now()->addDays(30);
        $surat->data = [
            'name' => $request->input('name'),
            'username' => $request->input('name'),
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
    }

    public function destroy(Surat $surat)
    {
        if ($surat->status == 'on_process') {
            Surat::destroy($surat->id);
            return redirect()->back()->with('success', 'Berhasil membatalkan pengajuan surat');
        }
        return redirect()->back()->with('deleted', 'Gagal membatalkan pengajuan surat');
    }
}
