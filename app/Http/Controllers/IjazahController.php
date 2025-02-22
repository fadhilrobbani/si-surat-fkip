<?php
// This class has name IjazahController, however ijazah object itself is based from Surat class
// So for naming convention we used surat instead of ijazah
namespace App\Http\Controllers;

use Exception;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class IjazahController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JenisSurat $jenisSurat)
    {
        if ($jenisSurat->slug == 'legalisir-ijazah') {

            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',

                'ijazah' => 'required|file|mimes:pdf|max:2048',
                'ktp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
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


            ];
            $data = $surat->data;

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
            $surat->files = [
                'ijazah' => $request->file('ijazah')->store('lampiran'),
                'ktp' => $request->file('ktp')->store('lampiran')
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
            $surat->data = $data;
            $surat->save();
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Pengajuan legalisir ijazah berhasil diajukan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Surat $surat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Surat $surat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Surat $surat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Surat $surat)
    {
        //
    }
}
