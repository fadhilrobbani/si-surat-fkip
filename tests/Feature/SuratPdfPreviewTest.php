<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratPdfPreviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_permohonan_narasumber_pdf_renders_successfully()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-permohonan-narasumber')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'selesai',
            'data' => [
                'nama' => $staff->name,
                'username' => $staff->username,
                'namaNarasumber' => 'Prof. Dr. John Doe',
                'jabatanNarasumber' => 'Guru Besar',
                'instansiNarasumber' => 'Universitas Indonesia',
                'namaKegiatan' => 'Seminar Pendidikan Nasional',
                'tempatKegiatan' => 'Aula FKIP',
                'hariTanggal' => 'Kamis, 22 Oktober 2026',
                'waktu' => '08.00 WIB s.d selesai',
                'temaMateri' => 'Transformasi Digital Pendidikan',
                'noSurat' => '', // Menguji penomoran kosong (..........)
                'private' => [
                    'namaDekan' => 'Dr. Dekan FKIP',
                    'nipDekan' => '197001011995011001'
                ]
            ]
        ]);

        $response = $this->actingAs($staff)->get('/staff/preview-surat/' . $surat->id);
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_peminjaman_ruang_pdf_renders_successfully()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-peminjaman-ruang-mahasiswa')->first();

        $surat = Surat::create([
            'pengaju_id' => $mhs->id,
            'current_user_id' => $mhs->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'selesai',
            'data' => [
                'nama' => $mhs->name,
                'username' => $mhs->username,
                'namaOrganisasi' => 'BEM FKIP',
                'jabatanPengaju' => 'Ketua Umum',
                'namaRuangan' => 'Aula Bukit Daun',
                'namaKegiatan' => 'Pentas Seni Mahasiswa',
                'hariTanggal' => 'Sabtu, 28 November 2026',
                'jamPemakaian' => '13.00 s.d 21.00 WIB',
                'jumlahPeserta' => 300,
                'noSurat' => '151',
                'private' => [
                    'namaWD2' => 'Dr. WD 2 FKIP',
                    'nipWD2' => '197501012000011002'
                ]
            ]
        ]);

        $response = $this->actingAs($mhs)->get('/mahasiswa/preview-surat/' . $surat->id);
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_pencairan_dana_pdf_renders_successfully()
    {
        $bendahara = User::where('role_id', User::ROLE_BENDAHARA)->first();
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-pencairan-dana-mahasiswa')->first();

        $surat = Surat::create([
            'pengaju_id' => $mhs->id,
            'current_user_id' => $mhs->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'selesai',
            'data' => [
                'nama' => $mhs->name,
                'username' => $mhs->username,
                'namaOrganisasi' => 'HIMA Pendidikan Kimia',
                'namaKegiatan' => 'Lomba Karya Ilmiah Remaja 2026',
                'tahunAnggaran' => 2026,
                'items' => [
                    ['uraian' => 'Konsumsi Juri dan Panitia', 'nominal' => 1200000],
                    ['uraian' => 'Piala dan Sertifikat Pemenang', 'nominal' => 800000],
                ],
                'totalAnggaran' => 2000000,
                'namaBank' => 'Bank Mandiri',
                'nomorRekening' => '1320019283741',
                'atasNamaRekening' => 'HIMA Kimia FKIP',
                'noSurat' => '',
                'nomorBuktiPencairan' => 'KAS/2026/XI/089',
                'private' => [
                    'namaWD2' => 'Dr. WD 2 FKIP',
                    'nipWD2' => '197501012000011002'
                ]
            ]
        ]);

        $response = $this->actingAs($bendahara)->get('/bendahara/preview-surat/' . $surat->id);
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
