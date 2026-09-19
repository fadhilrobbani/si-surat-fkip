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
            ->get('/kaprodi/surat-masuk')
            ->assertStatus(200);

        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-disetujui/' . $surat->id, [
                'penerima' => $wd3->id,
            ])
            ->assertRedirect('/kaprodi/surat-masuk');

        $surat->refresh();
        $this->assertEquals($wd3->id, $surat->current_user_id);
        $this->assertEquals([2, 4], $surat->data['private']['stepper']);

        // 3. WD3 -> WD2
        $this->actingAs($wd3)
            ->get('/wd3/surat-masuk')
            ->assertStatus(200);

        $this->actingAs($wd3)
            ->get(route('show-surat-wd3', $surat->id))
            ->assertStatus(200)
            ->assertSee('Wakil Dekan Bidang Kemahasiswaan')
            ->assertSee('Menunggu');

        $this->actingAs($wd3)
            ->put('/wd3/surat-disetujui/' . $surat->id, [
                'penerima' => $wd2->id,
            ])
            ->assertRedirect('/wd3/surat-masuk');

        $surat->refresh();
        $this->assertEquals($wd2->id, $surat->current_user_id);
        $this->assertEquals([2, 4, 10], $surat->data['private']['stepper']);

        // 4. WD2 -> TU
        $this->actingAs($wd2)
            ->get('/wd2/surat-masuk')
            ->assertStatus(200);

        $this->actingAs($wd2)
            ->get(route('show-surat-wd2', $surat->id))
            ->assertStatus(200)
            ->assertSee('Wakil Dekan Bidang Keuangan dan Umum')
            ->assertSee('Menunggu');

        $this->actingAs($wd2)
            ->put('/wd2/surat-disetujui/' . $surat->id, [
                'penerima' => $tu->id,
            ])
            ->assertRedirect('/wd2/surat-masuk');

        $surat->refresh();
        $this->assertEquals($tu->id, $surat->current_user_id);
        $this->assertEquals([2, 4, 10, 9], $surat->data['private']['stepper']);

        // 5. TU -> Verifikasi Dashboard & Surat Masuk
        $this->actingAs($tu)
            ->get('/tata-usaha')
            ->assertStatus(200)
            ->assertSee('Surat Masuk');

        $this->actingAs($tu)
            ->get('/tata-usaha/surat-masuk')
            ->assertStatus(200)
            ->assertSee('Aula Bukit Daun');

        $this->actingAs($tu)
            ->get(route('show-surat-masuk-tata-usaha', $surat->id))
            ->assertStatus(200)
            ->assertSee('Aula Bukit Daun')
            ->assertSee('Setujui Surat')
            ->assertSee('Tolak Surat');

        // TU -> Setujui & Selesaikan
        $this->actingAs($tu)
            ->post('/tata-usaha/surat-masuk/setujui/' . $surat->id, [
                'catatan' => 'Ruangan telah dijadwalkan dan siap digunakan.'
            ])
            ->assertRedirect('/tata-usaha/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals($mhs->id, $surat->current_user_id);
        $this->assertEquals([2, 4, 10, 9, 19], $surat->data['private']['stepper']);

        // 6. TU -> Riwayat Persetujuan
        $this->actingAs($tu)
            ->get('/tata-usaha/riwayat-persetujuan')
            ->assertStatus(200)
            ->assertSee('Disetujui');

        $approval = Approval::where('surat_id', $surat->id)->where('user_id', $tu->id)->first();
        $this->assertNotNull($approval);
        $this->assertEquals('Ruangan telah dijadwalkan dan siap digunakan.', $approval->note);

        $this->actingAs($tu)
            ->get(route('show-approval-tata-usaha', $approval->id))
            ->assertStatus(200)
            ->assertSee('Aula Bukit Daun');

        $this->actingAs($mhs)
            ->get(route('lihat-surat-mahasiswa', $surat->id))
            ->assertStatus(200)
            ->assertSee('Tata Usaha')
            ->assertSee('Disetujui')
            ->assertDontSee('Menunggu');
    }

    public function test_tata_usaha_can_reject_peminjaman_ruang()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $tu = User::where('role_id', User::ROLE_TATA_USAHA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-peminjaman-ruang-mahasiswa')->first();

        $surat = Surat::create([
            'pengaju_id' => $mhs->id,
            'jenis_surat_id' => $jenisSurat->id,
            'current_user_id' => $tu->id,
            'status' => 'diproses',
            'expired_at' => \Carbon\Carbon::now()->addDays(7),
            'data' => [
                'nama' => $mhs->name,
                'npm' => $mhs->username,
                'namaRuangan' => 'Aula Rektorat',
                'namaKegiatan' => 'Seminar Nasional',
                'private' => ['stepper' => [2, 4, 10, 9]]
            ]
        ]);

        $this->actingAs($tu)
            ->get(route('confirm-tolak-surat-tata-usaha', $surat->id))
            ->assertStatus(200)
            ->assertSee('Konfirmasi Penolakan');

        $this->actingAs($tu)
            ->post(route('tolak-surat-tata-usaha', $surat->id), [
                'catatan' => 'Ruangan bentrok dengan agenda rektorat.'
            ])
            ->assertRedirect('/tata-usaha/surat-masuk');

        $surat->refresh();
        $this->assertEquals('ditolak', $surat->status);
        $this->assertNull($surat->current_user_id);
        $this->assertEquals([2, 4, 10, 9, 19], $surat->data['private']['stepper']);

        $approval = Approval::where('surat_id', $surat->id)->where('user_id', $tu->id)->first();
        $this->assertNotNull($approval);
        $this->assertEquals(0, $approval->isApproved);
        $this->assertEquals('Ruangan bentrok dengan agenda rektorat.', $approval->note);

        $this->actingAs($tu)
            ->get('/tata-usaha/riwayat-persetujuan')
            ->assertStatus(200)
            ->assertSee('Ditolak');
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
