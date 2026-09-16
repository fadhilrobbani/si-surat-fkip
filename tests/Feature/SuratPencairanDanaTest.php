<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\Approval;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratPencairanDanaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_mahasiswa_can_view_form_pencairan_dana()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $this->actingAs($mhs)
            ->get('/mahasiswa/pengajuan-surat/surat-pencairan-dana-mahasiswa')
            ->assertStatus(200)
            ->assertSee('Surat Usulan Pengajuan / Pencairan Dana Kegiatan Mahasiswa');
    }

    public function test_mahasiswa_can_submit_pencairan_dana()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-pencairan-dana-mahasiswa')->first();

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($mhs)->post(route('store-surat', $jenisSurat->slug), [
            'name' => $mhs->name,
            'username' => $mhs->username,
            'penerima' => $kaprodi->id,
            'nama_organisasi' => 'BEM FKIP UNIB',
            'jabatan_pengaju' => 'Ketua Pelaksana',
            'nama_kegiatan' => 'Festival Budaya 2026',
            'tahun_anggaran' => 2026,
            'items' => [
                ['uraian' => 'Sewa Panggung & Tenda', 'nominal' => 2500000],
                ['uraian' => 'Konsumsi Peserta', 'nominal' => 1500000],
            ],
            'total_anggaran' => 4000000,
            'nama_bank' => 'Bank Bengkulu',
            'nomor_rekening' => '1234567890',
            'atas_nama_rekening' => 'BEM FKIP',
            'lampiran_proposal' => $file,
        ]);

        $response->assertRedirect('/mahasiswa/riwayat-pengajuan-surat');

        $this->assertDatabaseHas('surat_tables', [
            'pengaju_id' => $mhs->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
        ]);
    }

    public function test_pencairan_dana_approval_chain_to_bendahara()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $wd3 = User::where('role_id', User::ROLE_WD3)->first();
        $wd2 = User::where('role_id', User::ROLE_WD2)->first();
        $kabag = User::where('role_id', User::ROLE_KABAG)->first();
        $bendahara = User::where('role_id', User::ROLE_BENDAHARA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-pencairan-dana-mahasiswa')->first();

        // 1. Buat surat ajuan awal
        $surat = Surat::create([
            'pengaju_id' => $mhs->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $mhs->name,
                'username' => $mhs->username,
                'namaOrganisasi' => 'BEM FKIP',
                'jabatanPengaju' => 'Ketua Pelaksana',
                'namaKegiatan' => 'Gebyar FKIP 2026',
                'tahunAnggaran' => 2026,
                'items' => [
                    ['uraian' => 'Konsumsi', 'nominal' => 1000000]
                ],
                'totalAnggaran' => 1000000,
                'namaBank' => 'BNI',
                'nomorRekening' => '987654321',
                'atasNamaRekening' => 'BEM FKIP',
                'private' => [
                    'stepper' => [2]
                ]
            ]
        ]);

        // 2. Kaprodi menyetujui -> diteruskan ke WD3
        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-disetujui/' . $surat->id, [
                'penerima' => $wd3->id,
            ])
            ->assertRedirect('/kaprodi/surat-masuk');

        $surat->refresh();
        $this->assertEquals($wd3->id, $surat->current_user_id);
        $this->assertEquals('diproses', $surat->status);

        // 3. WD3 menyetujui -> diteruskan ke WD2
        $this->actingAs($wd3)
            ->put('/wd3/surat-disetujui/' . $surat->id, [
                'penerima' => $wd2->id,
            ])
            ->assertRedirect('/wd3/surat-masuk');

        $surat->refresh();
        $this->assertEquals($wd2->id, $surat->current_user_id);

        // 4. WD2 menyetujui -> diteruskan ke Kabag
        $this->actingAs($wd2)
            ->put('/wd2/surat-disetujui/' . $surat->id, [
                'penerima' => $kabag->id,
            ])
            ->assertRedirect('/wd2/surat-masuk');

        $surat->refresh();
        $this->assertEquals($kabag->id, $surat->current_user_id);

        // 5. Kabag menyetujui -> diteruskan ke Bendahara (status tetap diproses)
        $this->actingAs($kabag)
            ->put('/kabag/surat-disetujui/' . $surat->id)
            ->assertRedirect('/kabag/surat-masuk');

        $surat->refresh();
        $this->assertEquals($bendahara->id, $surat->current_user_id);
        $this->assertEquals('diproses', $surat->status);

        // 6. Bendahara menyetujui & mencairkan dana -> status SELESAI
        $this->actingAs($bendahara)
            ->put('/bendahara/surat-disetujui/' . $surat->id, [
                'no_bukti_pencairan' => 'KAS-FKIP/2026/001',
                'note' => 'Dana telah ditransfer ke rekening BEM FKIP'
            ])
            ->assertRedirect('/bendahara/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals('KAS-FKIP/2026/001', $surat->data['nomorBuktiPencairan']);
        $this->assertEquals($mhs->id, $surat->current_user_id);
    }

    public function test_bendahara_can_reject_pencairan_dana()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $bendahara = User::where('role_id', User::ROLE_BENDAHARA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-pencairan-dana-mahasiswa')->first();

        $surat = Surat::create([
            'pengaju_id' => $mhs->id,
            'current_user_id' => $bendahara->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'namaKegiatan' => 'Kegiatan Ditolak',
                'totalAnggaran' => 500000,
            ]
        ]);

        $this->actingAs($bendahara)
            ->put('/bendahara/surat-ditolak/' . $surat->id, [
                'note' => 'RAB tidak sesuai pagu anggaran fakultas'
            ])
            ->assertRedirect('/bendahara/surat-masuk');

        $surat->refresh();
        $this->assertEquals('ditolak', $surat->status);
        $this->assertEquals('RAB tidak sesuai pagu anggaran fakultas', $surat->data['alasanPenolakan']);
    }
}
