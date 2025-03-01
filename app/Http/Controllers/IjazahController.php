<?php
// This class has name IjazahController, however ijazah object itself is based from Surat class
// So for naming convention we used surat instead of ijazah
namespace App\Http\Controllers;

use App\Mail\OrderPembayaran;
use Illuminate\Support\Facades\Mail;

use Exception;
use Carbon\Carbon;

use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\Order;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class IjazahController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, JenisSurat $jenisSurat)
    {
        // $suratTerbaru = Surat::whereHas('jenisSurat', function ($query) {
        //     $query->where('slug', 'legalisir-ijazah'); // Filter berdasarkan slug di tabel jenis_surat
        // })
        //     ->select('id', 'expired_at') // Ambil hanya kolom yang diperlukan
        //     ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal pembuatan terbaru
        //     ->first(); // Ambil data pertama

        // if ($suratTerbaru->expired_at < now() && $suratTerbaru->expired_at != null) {
        //     return redirect()->back()->with('deleted', 'Maaf, pengajuan legalisir telah kadaluarsa. Silahkan ajukan yang baru.');
        // }



        if ($jenisSurat->slug == 'legalisir-ijazah' && $request->input('pengiriman') == 'dikirim') {

            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'ijazah' => 'required|file|mimes:pdf|max:2048',
                'ktp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'jumlah-lembar' => 'required|integer|min:1|max:10',
                'alamat' => 'required',
                'kode-pos' => 'required|integer|digits:5',
                'provinsi' => 'required',
                'kota' => 'required',
                'kecamatan' => 'required',
                'kelurahan' => 'required',
                'url-ongkir' => 'required|url',
                'total-harga' => 'required|numeric',

            ]);

            // Hapus format Rupiah dari ongkir
            $ongkir = str_replace(['Rp', '.'], '', $request->input('ongkir'));

            // Validasi ongkir sebagai numerik setelah pemformatan
            $request->merge(['ongkir' => $ongkir]);

            $request->validate([
                'ongkir' => 'required|numeric',
            ]);


            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = auth()->user()->id;
            // $surat->current_user_id = $request->input('penerima');
            $surat->status = 'menunggu_pembayaran';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(3);
            $totalHarga = $this->hitungHarga($request);
            $surat->data = [
                'metodePengiriman' => "Dikirim (JNE REG)",
                'pengiriman' => $request->input('pengiriman'),
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'jumlahLembar' => $request->input('jumlah-lembar'),
                'alamat' => $request->input('alamat'),
                'kodePos' => $request->input('kode-pos'),
                'provinsi' => $request->input('provinsi'),
                'kota' => $request->input('kota'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'ongkir' => $request->input('ongkir'),
                'biayaJasa' => 5000,
                'biayaLembar' => 2000 * $request->input('jumlah-lembar'),
                'totalHarga' => $totalHarga,
                'urlOngkir' => $request->input('url-ongkir'),
                'catatanUntukAkademik' => $request->input('catatan-mahasiswa')
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
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('status', 'diproses')
                            ->where('expired_at', '>', Carbon::now());
                    })
                        ->orWhere('status', 'dikirim')
                        ->orWhere(function ($query) {
                            $query->where('status', 'menunggu_pembayaran')
                                ->where('expired_at', '>', Carbon::now());
                        });
                })
                ->where('created_at', '>=', now()->subDays(122))
                ->count() > 0

            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki pengajuan legalisir yang sedang diproses, dikirim, atau menunggu pembayaran. Silahkan tunggu hingga selesai/ditolak, atau batalkan pengajuan sebelumnya.');
            }
            $surat->data = $data;
            $surat->save();
            Mail::to($surat->pengaju->email)->send(new OrderPembayaran($surat));

            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Pengajuan legalisir ijazah berhasil diajukan. Menunggu konfirmasi pembayaran agar dapat diproses lebih lanjut.');
        }

        if ($jenisSurat->slug == 'legalisir-ijazah' && $request->input('pengiriman') == 'ambil') {
            $request->validate([
                'name' => 'required',
                'username' => 'required',
                'program-studi' => 'required',
                'email' => 'required|email',
                'ijazah' => 'required|file|mimes:pdf|max:2048',
                'ktp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
                'jumlah-lembar' => 'required|integer|min:1|max:10',

                'total-harga' => 'required|numeric',

            ]);

            // Hapus format Rupiah dari ongkir
            // $ongkir = str_replace(['Rp', '.'], '', $request->input('ongkir'));

            // Validasi ongkir sebagai numerik setelah pemformatan
            // $request->merge(['ongkir' => $ongkir]);

            // $request->validate([
            //     'ongkir' => 'required|numeric',
            // ]);


            $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

            $surat = new Surat;
            $surat->pengaju_id = auth()->user()->id;
            $surat->current_user_id = auth()->user()->id;
            // $surat->current_user_id = $request->input('penerima');
            $surat->status = 'menunggu_pembayaran';
            $surat->jenis_surat_id = $jenisSurat->id;
            $surat->expired_at = now()->addDays(3);
            $totalHarga = $this->hitungHarga($request);
            $surat->data = [
                'metodePengiriman' => "Ambil di tempat (Akademik FKIP UNIB)",
                'pengiriman' => $request->input('pengiriman'),
                'nama' => $request->input('name'),
                'npm' => $request->input('username'),
                'programStudi' => $programStudi->name,
                'email' => $request->input('email'),
                'jumlahLembar' => $request->input('jumlah-lembar'),
                'biayaJasa' => 5000,
                'biayaLembar' => 2000 * $request->input('jumlah-lembar'),
                'totalHarga' => $totalHarga,
                'catatanUntukAkademik' => $request->input('catatan-mahasiswa')
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
                // Surat::where('jenis_surat_id', $jenisSurat->id)
                // ->where('pengaju_id', auth()->user()->id)
                // ->where(function ($query) {
                //     $query->where('status', 'diproses')
                //         ->orWhere('status', 'menunggu_pembayaran')
                //         ->orWhere('status', 'dikirim');
                // })
                // ->where('created_at', '>=', now()->subDays(30))
                // ->count() > 0

                // Surat::where('jenis_surat_id', $jenisSurat->id)
                // ->where('pengaju_id', auth()->user()->id)
                // ->where(function ($query) {
                //     $query->where('status', 'diproses')
                //         ->orWhere('status', 'dikirim')
                //         ->orWhere(function ($query) {
                //             $query->where('status', 'menunggu_pembayaran')
                //                 ->where('expired_at', '>', Carbon::now());
                //         });

                Surat::where('jenis_surat_id', $jenisSurat->id)
                ->where('pengaju_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('status', 'diproses')
                            ->where('expired_at', '>', Carbon::now());
                    })
                        ->orWhere('status', 'dikirim')
                        ->orWhere(function ($query) {
                            $query->where('status', 'menunggu_pembayaran')
                                ->where('expired_at', '>', Carbon::now());
                        });
                })
                ->where('created_at', '>=', now()->subDays(122))
                ->count() > 0

            ) {
                return redirect()->back()->with('deleted', 'Anda masih memiliki pengajuan legalisir yang sedang diproses, dikirim, atau menunggu pembayaran. Silahkan tunggu hingga selesai/ditolak, atau batalkan pengajuan sebelumnya.');
            }
            $surat->data = $data;
            $surat->save();
            Mail::to($surat->pengaju->email)->send(new OrderPembayaran($surat));

            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Pengajuan legalisir ijazah berhasil diajukan. Menunggu konfirmasi pembayaran agar dapat diproses lebih lanjut.');
        }
    }

    // public function storeOrder(Request $request, Surat $surat)
    // {

    //     $order = new Order();
    //     $order->status = 'menunggu_pembayaran';
    //     $order->expired_at = now()->addDays(2);
    //     $order->surat_id = $surat->id;
    //     $order->harga = $this->hitungHarga($request);
    //     $order->save();
    // }

    public function hitungHarga(Request $request)
    {
        $ongkir = 0;
        if ($request->input('pengiriman') == 'dikirim') {
            $request->validate([
                'ongkir' => 'required|numeric',
            ]);
            $ongkir = $request->input('ongkir');
        } elseif ($request->input('pengiriman') == 'ambil') {
            $ongkir = 0;
        }
        $jumlahLembar = $request->input('jumlah-lembar');
        $biayaJasa = 5000;
        $biayaLembar = 2000 * $jumlahLembar;
        $totalHarga = $ongkir + $biayaLembar + $biayaJasa;

        return $totalHarga;
    }

    public function konfirmasiPembayaran(Request $request, Surat $surat)
    {
        $action = $request->input('action');

        if ($action === 'konfirmasi') {
            // Logika konfirmasi pembayaran (upload bukti, dll.)
            $request->validate([
                'bukti-bayar' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $surat = Surat::find($surat->id);
            $buktiBayarPath = $request->file('bukti-bayar')->store('lampiran');

            $surat->files = array_merge((array)$surat->files, ['buktiBayar' => $buktiBayarPath]);
            $surat->status = 'diproses';
            $surat->current_user_id = $request->input('penerima');
            $surat->expired_at = now()->addDays(120);
            $surat->save();

            // ... logika lain ...
            return redirect()->back()->with('success', 'Pembayaran dikonfirmasi.');
        } elseif ($action === 'batal') {
            // Logika pembatalan surat
            Surat::destroy($surat->id);
            // ... logika lain ...
            return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Pengajuan legalisir berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Tindakan tidak valid.');
    }

    public function konfirmasiSelesai(Request $request, Surat $surat)
    {
        $surat->status = 'selesai';
        $data = $surat->data;
        $data['tanggalSelesai'] = formatTimestampToDateIndonesian(Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:s'));
        $surat->data = $data;
        $surat->current_user_id = auth()->user()->id;
        $surat->expired_at = null;
        $surat->save();

        // ... logika lain ...

        return redirect()->back()->with('success', 'Pengajuan selesai!');
    }


    // public function store(Request $request, JenisSurat $jenisSurat)
    // {
    //     if ($jenisSurat->slug == 'legalisir-ijazah') {

    //         $request->validate([
    //             'name' => 'required',
    //             'username' => 'required',
    //             'program-studi' => 'required',
    //             'email' => 'required|email',

    //             'ijazah' => 'required|file|mimes:pdf|max:2048',
    //             'ktp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
    //         ]);

    //         $programStudi = ProgramStudi::select('name')->where('id', '=', $request->input('program-studi'))->first();

    //         $surat = new Surat;
    //         $surat->pengaju_id = auth()->user()->id;
    //         $surat->current_user_id = $request->input('penerima');
    //         $surat->status = 'diproses';
    //         $surat->jenis_surat_id = $jenisSurat->id;
    //         $surat->expired_at = now()->addDays(30);
    //         $surat->data = [
    //             'nama' => $request->input('name'),
    //             'npm' => $request->input('username'),
    //             'programStudi' => $programStudi->name,
    //             'email' => $request->input('email'),


    //         ];
    //         $data = $surat->data;

    //         if ($data) {
    //             if (isset($data['private'])) {

    //                 $data['private']['stepper'][] = auth()->user()->role->id;
    //             } else {
    //                 $data['private'] = [
    //                     'stepper' => [auth()->user()->role->id]

    //                 ];
    //             }
    //         } else {
    //             $data = [
    //                 'private' => [
    //                     'stepper' => [auth()->user()->role->id]
    //                 ]
    //             ];
    //         }
    //         $surat->files = [
    //             'ijazah' => $request->file('ijazah')->store('lampiran'),
    //             'ktp' => $request->file('ktp')->store('lampiran')
    //         ];

    //         if (
    //             Surat::where('jenis_surat_id', $jenisSurat->id)
    //             ->where('pengaju_id', auth()->user()->id)
    //             ->where('status', 'diproses')
    //             ->where('created_at', '>=', now()->subDays(30)) // Menambahkan kondisi untuk created_at
    //             ->count() > 0
    //         ) {
    //             return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses. Silahkan tunggu hingga selesai/ditolak atau batalkan pengajuan sebelumnya');
    //         }
    //         $surat->data = $data;
    //         $surat->save();
    //         return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Pengajuan legalisir ijazah berhasil diajukan');
    //     }
    // }

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
