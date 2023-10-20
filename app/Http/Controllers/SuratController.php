<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            ]);
        }
        return abort(404);
    }

    public function storeSuratKeteranganAlumni(Request $request)
    {

        $pathSegments = explode('/', request()->path());
        $jenisSurat = end($pathSegments);
        // dd($jenisSurat);


        $validateSurat = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'program-studi' => 'required',
            'no-ijazah' => 'required',
            'birthplace' => 'required',
            'birthdate' => 'required|date',
            'email' => 'required|email'
        ]);


        // $staff = DB::select('SELECT*FROM users WHERE role_id =? AND program_studi_id  = ?', [3,3]);
        $staff = User::select('id')
            ->where('role_id', '=', 3)
            ->where('program_studi_id', '=', 3)
            ->first();
        // dd($staff);

        $kaprodi = User::select('id')
            ->where('role_id', '=', 4)
            ->where('program_studi_id', '=', 3)
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
        $surat->current_user_id = $staff->id;
        $surat->penerima_id = $kaprodi->id;
        $surat->status = 'on_process';
        $surat->jenis_surat_id = $jenisSurat;
        $surat->data = [
            'name' => $request->input('name'),
            'username' => $request->input('name'),
            'programStudi' => $request->input('program-studi'),
            'noIjazah' => $request->input('no-ijazah'),
            'birthplace' => $request->input('birthplace'),
            'birthdate' => $request->input('birthdate'),
            'email' => $request->input('email'),
        ];

        $surat->save();
        return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
    }
}
