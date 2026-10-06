<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratKolektifProdiTest extends TestCase
{
    use DatabaseTransactions;

    private function staff()
    {
        return User::where('role_id', User::ROLE_STAFF)->first();
    }

    private function kaprodi()
    {
        return User::where('role_id', User::ROLE_KAPRODI)->first();
    }

    private function staffDekan()
    {
        return User::where('role_id', User::ROLE_STAFF_DEKAN)->first();
    }

    public function test_staff_can_view_all_three_collective_form_pages()
    {
        $staff = $this->staff();

        foreach (['surat-cuti-mahasiswa', 'surat-izin-observasi', 'surat-penundaan-pembayaran-ukt'] as $slug) {
            $this->actingAs($staff)
                ->get('/staff/pengajuan-surat/' . $slug)
                ->assertStatus(200);
        }
    }

    public function test_staff_can_submit_surat_cuti_mahasiswa()
    {
        $staff = $this->staff();
        $kaprodi = $this->kaprodi();
        $jenisSurat = JenisSurat::where('slug', 'surat-cuti-mahasiswa')->first();

        $response = $this->actingAs($staff)->post(route('staff-store-surat-cuti-mahasiswa', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'tahun_akademik' => '2026/2027',
            'lama_cuti' => 2,
            'mahasiswa' => [
                ['nama' => 'Delia Gustina', 'npm' => 'A1D021019', 'program_studi' => 'S1 Pendidikan Biologi', 'alasan' => 'Bekerja'],
            ],
        ]);

        $response->assertRedirect('/staff/riwayat-pengajuan-surat');

        $surat = Surat::where('pengaju_id', $staff->id)
            ->where('jenis_surat_id', $jenisSurat->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($surat);
        $this->assertEquals($kaprodi->id, $surat->current_user_id);
        $this->assertEquals('diproses', $surat->status);
        $this->assertCount(1, $surat->data['mahasiswa']);
        $this->assertEquals([User::ROLE_STAFF], $surat->data['private']['stepper']);
    }

    public function test_staff_can_submit_surat_izin_observasi()
    {
        $staff = $this->staff();
        $kaprodi = $this->kaprodi();
        $jenisSurat = JenisSurat::where('slug', 'surat-izin-observasi')->first();

        $response = $this->actingAs($staff)->post(route('staff-store-surat-izin-observasi', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'mata_kuliah' => 'Inovasi Pembelajaran Biologi (BIO-340)',
            'tentang' => 'Analisis Kebutuhan dan Kurikulum',
            'hari_tanggal' => 'Rabu/ 26 Februari 2025',
            'pukul' => '08.00 WIB. s.d selesai',
            'tempat' => 'SMAN 08 Kota Bengkulu',
            'mahasiswa' => [
                ['nama' => 'Dinda Aprilia', 'npm' => 'A1D022001'],
                ['nama' => 'Anjelita Aktri Fortuna', 'npm' => 'A1D022021'],
            ],
        ]);

        $response->assertRedirect('/staff/riwayat-pengajuan-surat');

        $surat = Surat::where('pengaju_id', $staff->id)
            ->where('jenis_surat_id', $jenisSurat->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($surat);
        $this->assertEquals('SMAN 08 Kota Bengkulu', $surat->data['tempat']);
        $this->assertCount(2, $surat->data['mahasiswa']);
    }

    public function test_staff_can_submit_surat_penundaan_pembayaran_ukt()
    {
        $staff = $this->staff();
        $kaprodi = $this->kaprodi();
        $jenisSurat = JenisSurat::where('slug', 'surat-penundaan-pembayaran-ukt')->first();

        $response = $this->actingAs($staff)->post(route('staff-store-surat-penundaan-pembayaran-ukt', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'penerima' => $kaprodi->id,
            'nomor_surat_edaran' => '11308/UN30/AK/2026',
            'tanggal_surat_edaran' => '30 Juni 2026',
            'rentang_awal' => '3 Juli 2026',
            'rentang_akhir' => '4 Agustus 2026',
            'mahasiswa' => [
                ['nama' => 'Kuntum Khaira Ummah', 'npm' => 'A1D022058', 'hari_tanggal_ujian' => 'Senin, 6 Juli 2026', 'waktu' => '08.00-10.00'],
            ],
        ]);

        $response->assertRedirect('/staff/riwayat-pengajuan-surat');

        $surat = Surat::where('pengaju_id', $staff->id)
            ->where('jenis_surat_id', $jenisSurat->id)
            ->latest('created_at')
            ->first();

        $this->assertNotNull($surat);
        $this->assertCount(1, $surat->data['mahasiswa']);
        $this->assertEquals('08.00-10.00', $surat->data['mahasiswa'][0]['waktu']);
    }

    public function test_kaprodi_show_offers_staff_dekan_for_new_letters()
    {
        $staff = $this->staff();
        $kaprodi = $this->kaprodi();
        $staffDekan = $this->staffDekan();

        foreach (['surat-cuti-mahasiswa', 'surat-izin-observasi', 'surat-penundaan-pembayaran-ukt'] as $slug) {
            $jenisSurat = JenisSurat::where('slug', $slug)->first();
            $surat = Surat::create([
                'pengaju_id' => $staff->id,
                'current_user_id' => $kaprodi->id,
                'jenis_surat_id' => $jenisSurat->id,
                'status' => 'diproses',
                'data' => ['nama' => $staff->name, 'private' => ['stepper' => [User::ROLE_STAFF]]],
            ]);

            $view = $this->actingAs($kaprodi)->get(route('show-surat-kaprodi', $surat->id));
            $view->assertStatus(200);

            $penerima = collect($view->viewData('daftarPenerima'));
            $this->assertTrue($penerima->pluck('id')->contains($staffDekan->id), "Kaprodi recipient must contain Staff Dekan for {$slug}");
        }
    }

    public function test_kaprodi_approval_chain_to_staff_dekan_and_finalize_with_optional_number_and_date()
    {
        $staff = $this->staff();
        $kaprodi = $this->kaprodi();
        $staffDekan = $this->staffDekan();
        $jenisSurat = JenisSurat::where('slug', 'surat-cuti-mahasiswa')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'tahunAkademik' => '2026/2027',
                'lamaCuti' => 2,
                'mahasiswa' => [['nama' => 'Delia Gustina', 'npm' => 'A1D021019', 'programStudi' => 'S1 Pendidikan Biologi', 'alasanCuti' => 'Bekerja']],
                'private' => ['stepper' => [User::ROLE_STAFF]],
            ],
        ]);

        // Kaprodi meneruskan ke Staff Dekan
        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $staffDekan->id,
                'nama_kaprodi' => 'Dr. Budi Santoso, M.Pd.',
                'nip_kaprodi' => '198001012000011001',
            ])
            ->assertRedirect('/kaprodi/surat-masuk');

        $surat->refresh();
        $this->assertEquals($staffDekan->id, $surat->current_user_id);
        $this->assertEquals('Koordinator Program Studi', $surat->data['private']['deskripsiKaprodi']);

        // Staff Dekan mengisi nomor + tanggal dan menyelesaikan
        $this->actingAs($staffDekan)
            ->put('/staff-dekan/surat-disetujui/' . $surat->id, [
                'no-surat' => '179/DST/UN30.7.9/DT.00.00/2026',
                'tanggal-surat' => '2026-08-24',
                'note' => 'Diselesaikan',
            ])
            ->assertRedirect('/staff-dekan/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals('179/DST/UN30.7.9/DT.00.00/2026', $surat->data['noSurat']);
        $this->assertEquals('24 Agustus 2026', $surat->data['tanggal_selesai']);
        $this->assertEquals($staff->id, $surat->current_user_id);
    }

    public function test_staff_dekan_can_finalize_ukt_letter_without_number_and_date()
    {
        $staff = $this->staff();
        $staffDekan = $this->staffDekan();
        $jenisSurat = JenisSurat::where('slug', 'surat-penundaan-pembayaran-ukt')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staffDekan->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'mahasiswa' => [['nama' => 'Kuntum Khaira Ummah', 'npm' => 'A1D022058', 'hariTanggalUjian' => 'Senin, 6 Juli 2026', 'waktu' => '08.00-10.00']],
                'private' => ['stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI]],
            ],
        ]);

        $this->actingAs($staffDekan)
            ->put('/staff-dekan/surat-disetujui/' . $surat->id, [
                'no-surat' => '',
                'tanggal-surat' => '',
                'note' => 'Nomor & tanggal menyusul (cap manual)',
            ])
            ->assertRedirect('/staff-dekan/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEmpty($surat->data['noSurat']);
        $this->assertNull($surat->data['tanggal_selesai']);
    }

    public function test_pdf_preview_renders_for_all_three_new_letters()
    {
        $staff = $this->staff();
        $staffDekan = $this->staffDekan();

        $data = [
            'surat-cuti-mahasiswa' => [
                'nama' => $staff->name,
                'tahunAkademik' => '2026/2027',
                'lamaCuti' => 2,
                'mahasiswa' => [['nama' => 'Delia Gustina', 'npm' => 'A1D021019', 'programStudi' => 'S1 Pendidikan Biologi', 'alasanCuti' => 'Bekerja']],
            ],
            'surat-izin-observasi' => [
                'nama' => $staff->name,
                'mataKuliah' => 'Inovasi Pembelajaran Biologi (BIO-340)',
                'tentang' => 'Analisis Kebutuhan dan Kurikulum',
                'hariTanggal' => 'Rabu/ 26 Februari 2025',
                'pukul' => '08.00 WIB. s.d selesai',
                'tempat' => 'SMAN 08 Kota Bengkulu',
                'mahasiswa' => [['nama' => 'Dinda Aprilia', 'npm' => 'A1D022001', 'programStudi' => 'S1 Pendidikan Biologi']],
            ],
            'surat-penundaan-pembayaran-ukt' => [
                'nama' => $staff->name,
                'nomorSuratEdaran' => '11308/UN30/AK/2026',
                'tanggalSuratEdaran' => '30 Juni 2026',
                'rentangAwal' => '3 Juli 2026',
                'rentangAkhir' => '4 Agustus 2026',
                'mahasiswa' => [['nama' => 'Kuntum Khaira Ummah', 'npm' => 'A1D022058', 'hariTanggalUjian' => 'Senin, 6 Juli 2026', 'waktu' => '08.00-10.00']],
            ],
        ];

        foreach ($data as $slug => $payload) {
            $jenisSurat = JenisSurat::where('slug', $slug)->first();
            $payload['private'] = ['stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_STAFF_DEKAN]];

            $surat = Surat::create([
                'pengaju_id' => $staff->id,
                'current_user_id' => $staff->id,
                'jenis_surat_id' => $jenisSurat->id,
                'status' => 'selesai',
                'data' => $payload,
            ]);

            $response = $this->actingAs($staff)->get('/staff/preview-surat/' . $surat->id);
            $response->assertStatus(200);
            $this->assertEquals('application/pdf', $response->headers->get('content-type'), "PDF failed for {$slug}");
        }
    }

    public function test_ukt_attachment_page_signature_block_has_qr_when_selesai()
    {
        $staff = $this->staff();
        $jenisSurat = JenisSurat::where('slug', 'surat-penundaan-pembayaran-ukt')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'selesai',
            'data' => [
                'nama' => $staff->name,
                'nomorSuratEdaran' => '11308/UN30/AK/2026',
                'tanggalSuratEdaran' => '30 Juni 2026',
                'rentangAwal' => '3 Juli 2026',
                'rentangAkhir' => '4 Agustus 2026',
                'mahasiswa' => [['nama' => 'Kuntum Khaira Ummah', 'npm' => 'A1D022058', 'hariTanggalUjian' => 'Senin, 6 Juli 2026', 'waktu' => '08.00-10.00']],
                'private' => ['stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_STAFF_DEKAN]],
            ],
        ]);

        $html = view('template.surat-penundaan-pembayaran-ukt', ['surat' => $surat])->render();

        // Setelah page_break (halaman lampiran) harus ada blok tanda tangan ber-QR, bukan kosong.
        $lampiran = substr($html, strpos($html, 'page_break'));
        $this->assertStringContainsString('tandatangan', $lampiran, 'Blok tanda tangan lampiran hilang.');
        $this->assertStringContainsString('data:image/svg', $lampiran, 'QR tidak terpasang pada blok tanda tangan lampiran UKT.');
    }

    public function test_ukt_attachment_signature_block_has_no_qr_when_not_selesai()
    {
        $staff = $this->staff();
        $jenisSurat = JenisSurat::where('slug', 'surat-penundaan-pembayaran-ukt')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staff->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'mahasiswa' => [['nama' => 'Kuntum Khaira Ummah', 'npm' => 'A1D022058', 'hariTanggalUjian' => 'Senin, 6 Juli 2026', 'waktu' => '08.00-10.00']],
                'private' => ['stepper' => [User::ROLE_STAFF]],
            ],
        ]);

        $html = view('template.surat-penundaan-pembayaran-ukt', ['surat' => $surat])->render();
        $lampiran = substr($html, strpos($html, 'page_break'));

        $this->assertStringContainsString('tandatangan', $lampiran);
        $this->assertStringNotContainsString('data:image/svg', $lampiran, 'QR tidak boleh muncul sebelum surat selesai.');
    }
}
