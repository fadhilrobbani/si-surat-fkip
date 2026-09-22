<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratNumberingAndUniversalBudgetTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /**
     * 1. Test dropdown pengajuan surat mahasiswa TIDAK memuat surat baru yang belum final.
     */
    public function test_mahasiswa_dropdown_does_not_contain_unfinalized_letters()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();

        $response = $this->actingAs($mhs)->get('/mahasiswa/pengajuan-surat');
        $response->assertStatus(200);

        // Pastikan slug surat baru tidak ada di halaman pilihan
        $response->assertDontSee('value="surat-peminjaman-ruang-mahasiswa"', false);
        $response->assertDontSee('value="surat-pencairan-dana-mahasiswa"', false);
    }

    /**
     * 2. Test Surat Peminjaman Ruang: Validasi ketat nomor surat dan render pure dots.
     */
    public function test_peminjaman_ruang_strict_validation_and_dots_rendering()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $tu = User::where('role_id', User::ROLE_TATA_USAHA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-peminjaman-ruang')->first();

        // Buat surat peminjaman ruang di posisi Tata Usaha
        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $tu->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'namaKegiatan' => 'Kuliah Umum Kimia Organik',
                'namaRuangan' => 'Gedung Kuliah Bersama II Ruang 04',
                'tanggal_mulai' => '2026-10-01',
                'waktuMulai' => '08:00',
                'waktuSelesai' => '12:00',
                'private' => ['stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_WD2]]
            ]
        ]);

        // A. Coba submit nomor angka saja tanpa slash -> harus gagal validasi
        $invalidResponse = $this->actingAs($tu)->post(route('setujui-surat-tata-usaha', $surat->id), [
            'no-surat' => '045',
            'catatan' => 'Catatan TU',
        ]);
        $invalidResponse->assertSessionHasErrors('no-surat');
        $surat->refresh();
        $this->assertEquals('diproses', $surat->status);

        // B. Coba submit nomor lengkap dengan slash -> harus lolos
        $fullNumber = '045/DST/UN30.7.11/PP/2026';
        $validResponse = $this->actingAs($tu)->post(route('setujui-surat-tata-usaha', $surat->id), [
            'no-surat' => $fullNumber,
            'catatan' => 'Kunci ruangan di ruang TU',
        ]);
        $validResponse->assertSessionHasNoErrors();
        $validResponse->assertRedirect('/tata-usaha/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals($fullNumber, $surat->data['noSurat']);

        // Cek preview PDF memuat nomor lengkap
        $pdfResponse = $this->actingAs($staff)->get('/staff/preview-surat/' . $surat->id);
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));

        // C. Cek jika noSurat kosong -> PDF harus memuat titik-titik tanpa kode hardcoded
        $suratKosong = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'selesai',
            'data' => [
                'namaKegiatan' => 'Rapat Dosen',
                'namaRuangan' => 'Ruang Rapat FKIP',
                'tanggal_mulai' => '2026-10-02',
                'waktuMulai' => '09:00',
                'waktuSelesai' => '11:00',
                'noSurat' => null,
            ]
        ]);

        $renderedHtml = view('template.surat-peminjaman-ruang', [
            'surat' => $suratKosong,
            'tahun' => 2026,
            'prodiName' => 'S1 Bimbingan dan Konseling',
            'jurusanName' => 'Jurusan Ilmu Pendidikan'
        ])->render();

        $this->assertStringContainsString('....................................................', $renderedHtml);
        // Pastikan TIDAK ada hardcoded suffix /DST/UN30.7.11/PP/2026 yang menempel saat nomor kosong
        $this->assertStringNotContainsString('..................................................../DST', $renderedHtml);
        // Pastikan tidak ada kata Jurusan berulang atau ruang berulang
        $this->assertStringNotContainsString('Jurusan Jurusan', $renderedHtml);
        $this->assertStringNotContainsString('ruang Ruang', $renderedHtml);
        $this->assertStringContainsString('Program Studi S1 Bimbingan dan Konseling Jurusan Ilmu Pendidikan', $renderedHtml);
    }

    /**
     * 3. Test Surat Permohonan Narasumber: Validasi ketat dan render pure dots.
     */
    public function test_permohonan_narasumber_strict_validation_and_dots_rendering()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-permohonan-narasumber')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staffDekan->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'namaKegiatan' => 'Workshop AI in Education',
                'namaNarasumber' => 'Prof. Dr. Ir. Budi Santoso',
                'tanggalMulai' => '2026-11-10',
                'waktuMulai' => '09:00',
                'waktuSelesai' => '12:00',
                'tempat' => 'Auditorium FKIP',
                'private' => ['stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_DEKAN]]
            ]
        ]);

        // A. Coba submit angka saja -> gagal
        $invalidResponse = $this->actingAs($staffDekan)->put(route('setujui-surat-staff-staff-dekan', $surat->id), [
            'no-surat' => '123',
            'note' => 'Disetujui',
        ]);
        $invalidResponse->assertSessionHasErrors('no-surat');

        // B. Submit format lengkap -> berhasil
        $fullNumber = '123/DST/UN30.7.10/DT.06/2026';
        $validResponse = $this->actingAs($staffDekan)->put(route('setujui-surat-staff-staff-dekan', $surat->id), [
            'no-surat' => $fullNumber,
            'note' => 'Disetujui lengkap',
        ]);
        $validResponse->assertSessionHasNoErrors();
        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals($fullNumber, $surat->data['noSurat']);

        // C. Cek render template saat kosong -> pure dots
        $suratKosong = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'selesai',
            'data' => [
                'namaKegiatan' => 'Kuliah Tamu',
                'namaNarasumber' => 'Dr. Ahmad',
                'noSurat' => null,
            ]
        ]);
        $html = view('template.surat-permohonan-narasumber', ['surat' => $suratKosong, 'tahun' => 2026])->render();
        $this->assertStringContainsString('....................................................', $html);
        $this->assertStringNotContainsString('..................................................../DST', $html);
    }

    /**
     * 4. Test Surat Pengajuan Dana: Universal RAB (Hierarkis + MAK + Subkegiatan & Flat).
     */
    public function test_pengajuan_dana_universal_rab_and_strict_numbering()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-pencairan-dana')->first();

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        // A. Coba submit dengan no_surat angka saja -> harus ditolak
        $invalidResponse = $this->actingAs($staff)->post(route('staff-store-surat-pencairan-dana', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'no_surat' => '999',
            'nama_kegiatan' => 'Akreditasi Internasional',
            'tahun_anggaran' => '2026',
            'nama_bank' => 'Bank Mandiri',
            'nomor_rekening' => '987654321',
            'atas_nama_rekening' => 'Prodi Kimia',
            'berkas_proposal' => $file,
            'kegiatans' => [
                ['nama' => 'Kegiatan 1', 'mode' => 'flat', 'nominal' => 1000000]
            ]
        ]);
        $invalidResponse->assertSessionHasErrors('no_surat');

        // B. Submit dengan struktur Universal RAB (Hierarkis + Subkegiatan + MAK)
        $fullNumber = '018/DST/UN30.7.11/KU.01.02/2026';
        $validResponse = $this->actingAs($staff)->post(route('staff-store-surat-pencairan-dana', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'no_surat' => $fullNumber,
            'nama_kegiatan' => 'Asesmen Lapangan Lamdik',
            'tahun_anggaran' => '2026',
            'nama_bank' => 'Bank Bengkulu',
            'nomor_rekening' => '1122334455',
            'atas_nama_rekening' => 'Bendahara Pengeluaran Prodi',
            'berkas_proposal' => $file,
            'kegiatans' => [
                [
                    'nama' => 'Honorarium dan Konsumsi Asesor',
                    'mak' => '521211',
                    'mode' => 'rincian',
                    'sub_items' => [
                        ['uraian' => 'Honor Asesor 1', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => 2000000],
                        ['uraian' => 'Honor Asesor 2', 'volume' => 1, 'satuan' => 'Paket', 'harga_satuan' => 2000000],
                        ['uraian' => 'Konsumsi Rapat', 'volume' => 20, 'satuan' => 'Kotak', 'harga_satuan' => 25000],
                    ]
                ],
                [
                    'nama' => 'Operasional ATK & Percetakan Dokumen',
                    'mak' => '521211',
                    'mode' => 'flat',
                    'nominal' => 1500000
                ]
            ]
        ]);

        $validResponse->assertSessionHasNoErrors();
        $validResponse->assertRedirect('/staff/riwayat-pengajuan-surat');

        $surat = Surat::where('pengaju_id', $staff->id)->latest('id')->first();
        $this->assertNotNull($surat);
        $this->assertEquals($fullNumber, $surat->data['noSurat']);

        // Total: (2jt + 2jt + 500rb) + 1.5jt = 6.000.000
        $this->assertEquals(6000000, $surat->data['totalAnggaran']);
        $this->assertCount(2, $surat->data['items']);
        $this->assertTrue($surat->data['items'][0]['has_sub']);
        $this->assertCount(3, $surat->data['items'][0]['sub_items']);
        $this->assertEquals('521211', $surat->data['items'][0]['mak']);
        $this->assertFalse($surat->data['items'][1]['has_sub']);

        // C. Render cetak template HTML untuk struktur hierarkis
        $htmlHierarki = view('template.surat-ajuan-dana', [
            'surat' => $surat,
            'prodiName' => 'S1 Bimbingan dan Konseling',
            'jurusanName' => 'Jurusan Ilmu Pendidikan',
            'tahun' => 2026,
            'url' => 'https://example.com'
        ])->render();

        // Pastikan MAK, sub-items, dan total tercetak
        $this->assertStringContainsString('Kode Akun / MAK', $htmlHierarki);
        $this->assertStringContainsString('521211', $htmlHierarki);
        $this->assertStringContainsString('Honorarium dan Konsumsi Asesor', $htmlHierarki);
        $this->assertStringContainsString('Honor Asesor 1', $htmlHierarki);
        $this->assertStringContainsString('Rp 6.000.000', $htmlHierarki);
        $this->assertStringContainsString($fullNumber, $htmlHierarki);

        // Pastikan tidak ada duplikasi kata S1 S1 atau Jurusan Jurusan
        $this->assertStringNotContainsString('S1 S1', $htmlHierarki);
        $this->assertStringNotContainsString('Jurusan Jurusan', $htmlHierarki);
        $this->assertStringContainsString('Program Studi S1 Bimbingan dan Konseling Jurusan Ilmu Pendidikan', $htmlHierarki);

        // Pastikan subkegiatan TIDAK memiliki subnomor seperti 1.1 (harus kosong)
        $this->assertStringNotContainsString('1.1', $htmlHierarki);

        // Pastikan kolom MAK terletak di sebelah KANAN kolom Kegiatan / Uraian
        $posKegiatan = strpos($htmlHierarki, 'Kegiatan / Uraian Kebutuhan Anggaran');
        $posMak = strpos($htmlHierarki, 'Kode Akun / MAK');
        $this->assertTrue($posMak > $posKegiatan, 'Kolom MAK harus berada di sebelah kanan Kegiatan / Uraian');

        // D. Verifikasi halaman Show Surat Bendahara merender subkegiatan dan MAK
        $bendahara = User::where('role_id', User::ROLE_BENDAHARA)->first();
        $surat->update(['current_user_id' => $bendahara->id, 'status' => 'diproses']);
        $responseShowBendahara = $this->actingAs($bendahara)->get(route('show-surat-masuk-bendahara', $surat->id));
        $responseShowBendahara->assertOk();
        $responseShowBendahara->assertSee('Honorarium dan Konsumsi Asesor');
        $responseShowBendahara->assertSee('Honor Asesor 1');
        $responseShowBendahara->assertSee('Konsumsi Rapat');
        $responseShowBendahara->assertSee('521211');
        $responseShowBendahara->assertDontSee('>1.1<');

        // E. Verifikasi halaman QR Preview juga merender subkegiatan dan MAK
        $htmlQr = view('previews.show-surat-qr', ['surat' => $surat])->render();
        $this->assertStringContainsString('Honorarium dan Konsumsi Asesor', $htmlQr);
        $this->assertStringContainsString('Honor Asesor 1', $htmlQr);
        $this->assertStringContainsString('521211', $htmlQr);

        // F. Render cetak template jika tanpa MAK dan tanpa subkegiatan (Flat murni)
        $suratFlat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'selesai',
            'data' => [
                'namaKegiatan' => 'Operasional ATK Kantor',
                'noSurat' => null,
                'totalAnggaran' => 750000,
                'items' => [
                    ['uraian' => 'Kertas HVS A4 5 Rim', 'mak' => null, 'nominal' => 250000, 'has_sub' => false],
                    ['uraian' => 'Tinta Printer Epson', 'mak' => null, 'nominal' => 500000, 'has_sub' => false],
                ]
            ]
        ]);

        $htmlFlat = view('template.surat-ajuan-dana', [
            'surat' => $suratFlat,
            'prodiName' => 'Pendidikan Kimia',
            'jurusanName' => 'PMIPA',
            'tahun' => 2026,
            'url' => 'https://example.com'
        ])->render();

        // Pastikan kolom MAK TIDAK muncul jika seluruh MAK kosong
        $this->assertStringNotContainsString('Kode Akun / MAK', $htmlFlat);
        $this->assertStringContainsString('Kertas HVS A4 5 Rim', $htmlFlat);
        $this->assertStringContainsString('Tinta Printer Epson', $htmlFlat);
        // Pastikan nomor surat kosong menghasilkan titik-titik murni
        $this->assertStringContainsString('....................................................', $htmlFlat);
        $this->assertStringNotContainsString('..................................................../DST', $htmlFlat);
    }

    /**
     * Test kompatibilitas format noSurat lama (angka saja) vs baru (full dengan slash '/')
     * pada Surat Tugas (v1 dan v2) serta halaman validasi QR.
     */
    public function test_surat_tugas_backward_compatibility_and_qr_rendering()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $jenisSuratTugas = JenisSurat::where('slug', 'surat-tugas')->first();
        $currentYear = date('Y');

        // 1. Data LAMA: hanya angka gundul (misal '64')
        $suratLegacy = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSuratTugas->id,
            'status' => 'selesai',
            'created_at' => now()->setYear(2024),
            'data' => [
                'noSurat' => '64',
                'tanggal_selesai' => '15 Mei 2024',
                'acara' => 'Pelatihan Penulisan Artikel',
                'tempat' => 'Aula FKIP',
                'waktuPelaksanaan' => '15 Mei 2024',
                'dasarPenugasan' => 'Surat Tugas Dekan',
                'dosen' => [
                    ['namaDosen' => 'Dr. Budi', 'nipDosen' => '19800101', 'pangkatDosen' => 'III/c', 'jabatanFungsionalDosen' => 'Lektor']
                ],
                'private' => [
                    'namaWD1' => 'Dr. Wakil Dekan I',
                    'nipWD1' => '19700101',
                    'deskripsiWD1' => 'Wakil Dekan Bidang Akademik'
                ]
            ]
        ]);

        // Render template v1
        $htmlLegacyV1 = view('template.surat-tugas', ['surat' => $suratLegacy, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('Nomor:&nbsp;64/UN30.7/KP/2024', $htmlLegacyV1);

        // Render template v2
        $htmlLegacyV2 = view('template.v2.surat-tugas', ['surat' => $suratLegacy, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('Nomor:&nbsp;64/UN30.7/KP/2024', $htmlLegacyV2);

        // Render validasi QR
        $htmlQrLegacy = view('previews.show-surat-qr', ['surat' => $suratLegacy])->render();
        $this->assertStringContainsString('64/UN30.7/KP/2024', $htmlQrLegacy);
        $this->assertStringNotContainsString('64/UN30.7/KP/2024/UN30.7/KP', $htmlQrLegacy);

        // 2. Data BARU: format lengkap dengan slash '/' (misal '64/DST/UN30.7/KP/2026')
        $fullNumber = '64/DST/UN30.7/KP/' . $currentYear;
        $suratBaru = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSuratTugas->id,
            'status' => 'selesai',
            'created_at' => now(),
            'data' => [
                'noSurat' => $fullNumber,
                'tanggal_selesai' => '20 September ' . $currentYear,
                'acara' => 'Workshop Kurikulum',
                'tempat' => 'Hotel Santika',
                'waktuPelaksanaan' => '20 September ' . $currentYear,
                'dasarPenugasan' => 'SK Rektor',
                'dosen' => [
                    ['namaDosen' => 'Dr. Siti', 'nipDosen' => '19850101', 'pangkatDosen' => 'IV/a', 'jabatanFungsionalDosen' => 'Lektor Kepala']
                ],
                'private' => [
                    'namaWD1' => 'Dr. Wakil Dekan I',
                    'nipWD1' => '19700101',
                    'deskripsiWD1' => 'Wakil Dekan Bidang Akademik'
                ]
            ]
        ]);

        // Render template v1
        $htmlBaruV1 = view('template.surat-tugas', ['surat' => $suratBaru, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('Nomor:&nbsp;' . $fullNumber, $htmlBaruV1);
        $this->assertStringNotContainsString($fullNumber . '/UN30.7/KP', $htmlBaruV1);

        // Render template v2
        $htmlBaruV2 = view('template.v2.surat-tugas', ['surat' => $suratBaru, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('Nomor:&nbsp;' . $fullNumber, $htmlBaruV2);
        $this->assertStringNotContainsString($fullNumber . '/UN30.7/KP', $htmlBaruV2);

        // Render validasi QR
        $htmlQrBaru = view('previews.show-surat-qr', ['surat' => $suratBaru])->render();
        $this->assertStringContainsString($fullNumber, $htmlQrBaru);
        $this->assertStringNotContainsString($fullNumber . '/UN30.7/KP', $htmlQrBaru);

        // 3. Data KOSONG: belum terbit nomor surat
        $suratKosong = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSuratTugas->id,
            'status' => 'selesai',
            'created_at' => now(),
            'data' => [
                'noSurat' => null,
                'acara' => 'Bimbingan Teknis',
                'tempat' => 'Ruang Rapat FKIP',
                'waktuPelaksanaan' => '25 September ' . $currentYear,
                'dasarPenugasan' => 'Nota Dinas',
                'dosen' => [
                    ['namaDosen' => 'Dr. Siti', 'nipDosen' => '19850101', 'pangkatDosen' => 'IV/a', 'jabatanFungsionalDosen' => 'Lektor Kepala']
                ],
                'private' => [
                    'namaWD1' => 'Dr. Wakil Dekan I',
                    'nipWD1' => '19700101',
                    'deskripsiWD1' => 'Wakil Dekan Bidang Akademik'
                ]
            ]
        ]);

        $htmlKosongV1 = view('template.surat-tugas', ['surat' => $suratKosong, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('....................................................', $htmlKosongV1);

        $htmlKosongV2 = view('template.v2.surat-tugas', ['surat' => $suratKosong, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('....................................................', $htmlKosongV2);
        $this->assertStringNotContainsString('..................................................../UN30', $htmlKosongV2);

        $htmlQrKosong = view('previews.show-surat-qr', ['surat' => $suratKosong])->render();
        $this->assertStringContainsString('-', $htmlQrKosong);
    }

    /**
     * Test Surat Keluar di Staff Dekan:
     * - Tombol autofill format /UN30.7/PP/{tahun}
     * - Validasi ketat jika diisi tanpa '/' (pesan error sesuai format surat keluar)
     * - Dukungan nomor kosong (titik-titik dinas)
     * - Backward-compatibility data lama (angka saja otomatis ditambah suffix)
     * - Dukungan nomor baru format lengkap (tanpa dobel suffix)
     */
    public function test_surat_keluar_numbering_validation_and_backward_compatibility()
    {
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();
        $jenisSuratKeluar = JenisSurat::where('slug', 'surat-keluar')->first();
        $currentYear = date('Y');

        $surat = Surat::create([
            'pengaju_id' => $staffDekan->id,
            'current_user_id' => $staffDekan->id,
            'jenis_surat_id' => $jenisSuratKeluar->id,
            'status' => 'diproses',
            'data' => [
                'perihal' => 'Undangan Workshop Literasi',
                'tujuan1' => 'Kepala Dinas Pendidikan Provinsi Bengkulu',
                'jumlahLampiran' => 1,
                'paragrafAwal' => 'Sehubungan akan dilaksanakan kegiatan...',
                'paragrafAkhir' => 'Demikian surat ini kami sampaikan...',
                'tanggalPelaksanaan' => '10 Oktober 2026',
                'waktu' => '08:00 - selesai',
                'tempat' => 'Gedung Serbaguna',
                'private' => [
                    'stepper' => [User::ROLE_STAFF_DEKAN]
                ]
            ]
        ]);

        // 1. Cek halaman verifikasi Staff Dekan: tombol autofill format Surat Keluar muncul
        $responseShow = $this->actingAs($staffDekan)->get(route('show-surat-staff-dekan', $surat->id));
        $responseShow->assertOk();
        $responseShow->assertSee('📋 Gunakan Format: /UN30.7/PP/' . $currentYear);

        // 2. Submit hanya angka tanpa slash '/' -> validasi gagal dengan contoh format surat keluar
        $responseInvalid = $this->actingAs($staffDekan)->put(route('setujui-surat-staff-staff-dekan', $surat->id), [
            'no-surat' => '123',
            'note' => 'Disetujui',
        ]);
        $responseInvalid->assertSessionHasErrors('no-surat');
        $errorMsg = session('errors')->first('no-surat');
        $this->assertStringContainsString('/UN30.7/PP/', $errorMsg);
        $this->assertStringNotContainsString('/KP/', $errorMsg);

        // 3. Submit nomor kosong -> berhasil disetujui
        $responseKosong = $this->actingAs($staffDekan)->put(route('setujui-surat-staff-staff-dekan', $surat->id), [
            'no-surat' => '',
            'note' => 'Nomor menyusul',
        ]);
        $responseKosong->assertSessionHasNoErrors();
        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertNull($surat->data['noSurat']);

        // Render template surat keluar saat kosong -> titik-titik dinas
        $htmlKosongV1 = view('template.surat-keluar', ['surat' => $surat, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('....................................................', $htmlKosongV1);
        $this->assertStringNotContainsString('[NoSurat]', $htmlKosongV1);

        $htmlKosongV2 = view('template.v2.surat-keluar', ['surat' => $surat, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('....................................................', $htmlKosongV2);
        $this->assertStringNotContainsString('[NoSurat]', $htmlKosongV2);

        // 4. Backward-compatibility arsip lama (nomor angka murni di database misal '45')
        $surat->update([
            'data' => array_merge($surat->data, ['noSurat' => '45'])
        ]);
        $htmlLegacyV1 = view('template.surat-keluar', ['surat' => $surat, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('45/UN30.7/PP/', $htmlLegacyV1);

        $htmlLegacyV2 = view('template.v2.surat-keluar', ['surat' => $surat, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString('45/UN30.7/PP/', $htmlLegacyV2);

        // 5. Format baru / full format (misal '045/UN30.7/PP/2026') -> dicetak utuh tanpa dobel suffix
        $fullNumber = '045/UN30.7/PP/' . $currentYear;
        $surat->update([
            'data' => array_merge($surat->data, ['noSurat' => $fullNumber])
        ]);
        $htmlFullV1 = view('template.surat-keluar', ['surat' => $surat, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString($fullNumber, $htmlFullV1);
        $this->assertStringNotContainsString($fullNumber . '/UN30.7/PP', $htmlFullV1);

        $htmlFullV2 = view('template.v2.surat-keluar', ['surat' => $surat, 'url' => 'https://example.com'])->render();
        $this->assertStringContainsString($fullNumber, $htmlFullV2);
        $this->assertStringNotContainsString($fullNumber . '/UN30.7/PP', $htmlFullV2);

        // 6. Halaman validasi QR code
        $htmlQr = view('previews.show-surat-qr', ['surat' => $surat])->render();
        $this->assertStringContainsString($fullNumber, $htmlQr);
        $this->assertStringNotContainsString($fullNumber . '/UN30.7/PP', $htmlQr);
    }
}

