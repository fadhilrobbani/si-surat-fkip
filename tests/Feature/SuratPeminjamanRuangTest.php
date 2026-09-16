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

class SuratPeminjamanRuangTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_mahasiswa_can_view_form_peminjaman_ruang()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $this->actingAs($mhs)
            ->get('/mahasiswa/pengajuan-surat/surat-peminjaman-ruang-mahasiswa')
            ->assertStatus(200)
            ->assertSee('Surat Permohonan Peminjaman Ruang Kegiatan Mahasiswa');
    }

    public function test_mahasiswa_can_submit_peminjaman_ruang()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-peminjaman-ruang-mahasiswa')->first();

        $response = $this->actingAs($mhs)->post(route('store-surat', $jenisSurat->slug), [
            'name' => $mhs->name,
            'username' => $mhs->username,
            'penerima' => $kaprodi->id,
            'nama_organisasi' => 'HIMA Pendidikan Fisika',
            'jabatan_pengaju' => 'Ketua Panitia',
            'nama_ruangan' => 'Aula Bukit Daun',
            'nama_kegiatan' => 'Seminar Fisika 2026',
            'hari_tanggal' => 'Senin, 12 Oktober 2026',
            'jam_pemakaian' => '08.00 s.d 15.00 WIB',
            'jumlah_peserta' => 150,
        ]);

        $response->assertRedirect('/mahasiswa/riwayat-pengajuan-surat');

        $this->assertDatabaseHas('surat_tables', [
            'pengaju_id' => $mhs->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
        ]);
    }

    public function test_peminjaman_ruang_mahasiswa_approval_chain_to_tata_usaha()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $wd3 = User::where('role_id', User::ROLE_WD3)->first();
        $wd2 = User::where('role_id', User::ROLE_WD2)->first();
        $tu = User::where('role_id', User::ROLE_TATA_USAHA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-peminjaman-ruang-mahasiswa')->first();

        // 1. Buat surat
        $surat = Surat::create([
            'pengaju_id' => $mhs->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $mhs->name,
                'username' => $mhs->username,
                'namaRuangan' => 'Aula Bukit Daun',
                'namaKegiatan' => 'Malam Keakraban',
                'private' => ['stepper' => [2]]
            ]
        ]);

        // 2. Kaprodi -> WD3
        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-disetujui/' . $surat->id, [
                'penerima' => $wd3->id,
            ])
            ->assertRedirect('/kaprodi/surat-masuk');

        $surat->refresh();
        $this->assertEquals($wd3->id, $surat->current_user_id);

        // 3. WD3 -> WD2
        $this->actingAs($wd3)
            ->put('/wd3/surat-disetujui/' . $surat->id, [
                'penerima' => $wd2->id,
            ])
            ->assertRedirect('/wd3/surat-masuk');

        $surat->refresh();
        $this->assertEquals($wd2->id, $surat->current_user_id);

        // 4. WD2 -> TU
        $this->actingAs($wd2)
            ->put('/wd2/surat-disetujui/' . $surat->id, [
                'penerima' => $tu->id,
            ])
            ->assertRedirect('/wd2/surat-masuk');

        $surat->refresh();
        $this->assertEquals($tu->id, $surat->current_user_id);

        // 5. TU -> Setujui & Selesaikan
        $this->actingAs($tu)
            ->post('/tata-usaha/surat-masuk/setujui/' . $surat->id, [
                'note' => 'Ruangan telah dijadwalkan dan siap digunakan.'
            ])
            ->assertRedirect('/tata-usaha/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals($mhs->id, $surat->current_user_id);
    }

    public function test_staff_can_submit_peminjaman_ruang_and_chain_to_tata_usaha()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $wd2 = User::where('role_id', User::ROLE_WD2)->first();
        $tu = User::where('role_id', User::ROLE_TATA_USAHA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-peminjaman-ruang')->first();

        // 1. Submit by staff
        $response = $this->actingAs($staff)->post(route('staff-store-surat-peminjaman-ruang', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'nama_ruangan' => 'Ruang Rapat Dekanat',
            'nama_kegiatan' => 'Rapat Dosen Prodi',
            'hari_tanggal' => 'Jumat, 20 Oktober 2026',
            'jam_pemakaian' => '09.00 s.d 11.30 WIB',
        ]);

        $response->assertRedirect('/staff/riwayat-pengajuan-surat');

        $surat = Surat::where('pengaju_id', $staff->id)
            ->where('jenis_surat_id', $jenisSurat->id)
            ->latest()
            ->first();

        $this->assertNotNull($surat);
        $this->assertEquals($kaprodi->id, $surat->current_user_id);

        // 2. Kaprodi -> WD2
        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $wd2->id,
            ]);

        $surat->refresh();
        $this->assertEquals($wd2->id, $surat->current_user_id);

        // 3. WD2 -> TU
        $this->actingAs($wd2)
            ->put('/wd2/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $tu->id,
            ]);

        $surat->refresh();
        $this->assertEquals($tu->id, $surat->current_user_id);

        // 4. TU approves -> selesai
        $this->actingAs($tu)
            ->post('/tata-usaha/surat-masuk/setujui/' . $surat->id, [
                'note' => 'Ruangan disetujui untuk rapat dosen.'
            ]);

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
    }
}
