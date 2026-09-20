<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\Approval;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratPermohonanNarasumberTest extends TestCase
{
    use DatabaseTransactions;

    public function test_staff_can_view_form_permohonan_narasumber()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $this->actingAs($staff)
            ->get('/staff/pengajuan-surat/surat-permohonan-narasumber')
            ->assertStatus(200)
            ->assertSee('Surat Permohonan Menjadi Narasumber');
    }

    public function test_staff_can_submit_permohonan_narasumber()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-permohonan-narasumber')->first();

        $response = $this->actingAs($staff)->post(route('staff-store-surat-permohonan-narasumber', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'nama_narasumber' => 'Prof. Dr. Ir. Budi Santoso, M.Eng.',
            'instansi_narasumber' => 'Institut Teknologi Bandung',
            'jabatan_narasumber' => 'Guru Besar Teknik',
            'nama_kegiatan' => 'Workshop Penulisan Jurnal Internasional',
            'tempat_kegiatan' => 'Gedung Serbaguna FKIP',
            'hari_tanggal' => 'Rabu, 15 November 2026',
            'waktu' => '09.00 s.d 13.00 WIB',
            'tema_materi' => 'Strategi Publikasi di Jurnal Bereputasi Q1',
        ]);

        $response->assertRedirect('/staff/riwayat-pengajuan-surat');

        $this->assertDatabaseHas('surat_tables', [
            'pengaju_id' => $staff->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
        ]);
    }

    public function test_permohonan_narasumber_approval_chain_to_staff_dekan_with_optional_no_surat()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $dekan = User::where('role_id', User::ROLE_DEKAN)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-permohonan-narasumber')->first();

        // 1. Buat surat
        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'username' => $staff->username,
                'namaNarasumber' => 'Dr. Jane Doe',
                'namaKegiatan' => 'Kuliah Umum FKIP',
                'hariTanggal' => 'Senin, 10 November 2026',
                'waktu' => '08.00 s.d 12.00 WIB',
                'tempatKegiatan' => 'Auditorium FKIP',
                'temaMateri' => 'Pendidikan Inklusif Abad 21',
                'private' => ['stepper' => [3]]
            ]
        ]);

        // 2. Kaprodi menyetujui -> diteruskan ke Dekan
        $this->actingAs($kaprodi)
            ->get('/kaprodi/surat-masuk')
            ->assertStatus(200);

        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $dekan->id,
            ])
            ->assertRedirect('/kaprodi/surat-masuk');

        $surat->refresh();
        $this->assertEquals($dekan->id, $surat->current_user_id);

        // 3. Dekan menyetujui -> diteruskan ke Staff Dekan
        $this->actingAs($dekan)
            ->get('/dekan/surat-masuk')
            ->assertStatus(200);

        $this->actingAs($dekan)
            ->put('/dekan/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $staffDekan->id,
            ])
            ->assertRedirect('/dekan/surat-masuk');

        $surat->refresh();
        $this->assertEquals($staffDekan->id, $surat->current_user_id);

        // 4. Staff Dekan menyetujui dan menyelesaikan DENGAN nomor surat dikosongkan (opsional)
        $this->actingAs($staffDekan)
            ->get('/staff-dekan/surat-masuk')
            ->assertStatus(200);

        $this->actingAs($staffDekan)
            ->put('/staff-dekan/surat-disetujui/' . $surat->id, [
                'no-surat' => '', // Dikosongkan sesuai permintaan WD1
                'note' => 'Diselesaikan, nomor surat akan diisi manual di TU'
            ])
            ->assertRedirect('/staff-dekan/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEmpty($surat->data['noSurat']);
        $this->assertEquals($staff->id, $surat->current_user_id);
    }

    public function test_staff_submits_permohonan_narasumber_with_tempat_kegiatan_and_optional_jabatan()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-permohonan-narasumber')->first();

        $response = $this->actingAs($staff)->post(route('staff-store-surat-permohonan-narasumber', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'nama_narasumber' => 'Prof. Dr. Ir. Budi Santoso, M.Eng.',
            'instansi_narasumber' => 'Institut Teknologi Bandung',
            'nama_kegiatan' => 'Workshop Penulisan Jurnal Internasional',
            'tempat_kegiatan' => 'Ruang Rapat Dekanat FKIP',
            'hari_tanggal' => 'Rabu, 15 November 2026',
            'waktu' => '09.00 s.d 13.00 WIB',
            'tema_materi' => 'Strategi Publikasi di Jurnal Bereputasi Q1',
        ]);

        $response->assertRedirect('/staff/riwayat-pengajuan-surat');

        $surat = Surat::where('pengaju_id', $staff->id)
            ->where('jenis_surat_id', $jenisSurat->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($surat);
        $this->assertEquals('Ruang Rapat Dekanat FKIP', $surat->data['tempatKegiatan']);
        $this->assertNull($surat->data['jabatanNarasumber']);
    }
}

