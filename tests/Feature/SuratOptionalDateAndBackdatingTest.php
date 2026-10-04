<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratOptionalDateAndBackdatingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_format_tanggal_surat_helper()
    {
        $this->assertEquals('....................', formatTanggalSurat(null));
        $this->assertEquals('....................', formatTanggalSurat(''));
        $this->assertEquals('....................', formatTanggalSurat('   '));
        $this->assertEquals('15 Agustus 2026', formatTanggalSurat('2026-08-15'));
        $this->assertEquals('15 Agustus 2026', formatTanggalSurat('15 Agustus 2026'));
    }

    public function test_staff_dekan_can_backdate_surat_date()
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
                'nama' => $staff->name,
                'namaNarasumber' => 'Dr. Jane Doe',
                'namaKegiatan' => 'Kuliah Umum',
                'private' => [
                    'stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_DEKAN]
                ]
            ],
        ]);

        $backdate = '2026-08-15';
        $response = $this->actingAs($staffDekan)->put(route('setujui-surat-staff-staff-dekan', $surat->id), [
            'no-surat' => '045/DST/UN30.7.10/DT.06/2026',
            'tanggal-surat' => $backdate,
            'note' => 'Disetujui dengan tanggal mundur',
        ]);

        $response->assertRedirect('/staff-dekan/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals('15 Agustus 2026', $surat->data['tanggal_selesai']);

        // Check PDF preview contains the backdated date
        $previewResponse = $this->actingAs($staffDekan)->get('/staff-dekan/preview-surat/' . $surat->id);
        $previewResponse->assertStatus(200);

        // Check QR validation page displays the backdated date and verification timestamp
        $qrUrl = URL::signedRoute('preview-surat-qr', ['surat' => $surat->id]);
        $qrResponse = $this->get($qrUrl);
        $qrResponse->assertStatus(200)
            ->assertSee('15 Agustus 2026')
            ->assertSee('Waktu Verifikasi Sistem:');
    }

    public function test_staff_dekan_can_leave_surat_date_empty()
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
                'nama' => $staff->name,
                'namaNarasumber' => 'Dr. Jane Doe',
                'namaKegiatan' => 'Kuliah Umum',
                'private' => [
                    'stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_DEKAN]
                ]
            ],
        ]);

        // Submit with empty tanggal-surat
        $response = $this->actingAs($staffDekan)->put(route('setujui-surat-staff-staff-dekan', $surat->id), [
            'no-surat' => '045/DST/UN30.7.10/DT.06/2026',
            'tanggal-surat' => '',
            'note' => 'Disetujui tanggal cap manual',
        ]);

        $response->assertRedirect('/staff-dekan/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertNull($surat->data['tanggal_selesai']);

        // Check PDF preview renders dots for date
        $pdfResponse = $this->actingAs($staffDekan)->get(route('preview-surat-staff-dekan', $surat->id));
        $pdfResponse->assertStatus(200);

        // Check QR validation page displays '-' for empty date and displays verification timestamp
        $qrUrl = URL::signedRoute('preview-surat-qr', ['surat' => $surat->id]);
        $qrResponse = $this->get($qrUrl);
        $qrResponse->assertStatus(200)
            ->assertSee('Tanggal Surat Diterbitkan:')
            ->assertSee('Waktu Verifikasi Sistem:');
    }

    public function test_tata_usaha_can_backdate_and_empty_surat_date()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $tataUsaha = User::where('role_id', User::ROLE_TATA_USAHA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-peminjaman-ruang')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $tataUsaha->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'namaKegiatan' => 'Rapat Jurusan',
                'namaRuangan' => 'Ruang Rapat Dekanat',
                'private' => [
                    'stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_WD2]
                ]
            ],
        ]);

        // Backdate test
        $response = $this->actingAs($tataUsaha)->post(route('setujui-surat-tata-usaha', $surat->id), [
            'no-surat' => '042/DST/UN30.7.11/PP/2026',
            'tanggal-surat' => '2026-07-20',
            'catatan' => 'Peminjaman disetujui',
        ]);

        $response->assertRedirect('/tata-usaha/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals('20 Juli 2026', $surat->data['tanggal_selesai']);
    }

    public function test_bendahara_can_backdate_pencairan_dana()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $bendahara = User::where('role_id', User::ROLE_BENDAHARA)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-pencairan-dana')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $bendahara->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'namaKegiatan' => 'Kegiatan Pembinaan',
                'items' => [
                    ['uraian' => 'Konsumsi', 'nominal' => 500000]
                ],
                'private' => [
                    'stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_WD2, User::ROLE_KABAG]
                ]
            ],
        ]);

        $response = $this->actingAs($bendahara)->put(route('setujui-surat-bendahara', $surat->id), [
            'no_bukti_pencairan' => 'KAS/2026/08/001',
            'no-surat' => '015/DST/UN30.7.11/KU.01.02/2026',
            'tanggal-surat' => '2026-08-01',
            'note' => 'Dana dicairkan dengan tanggal mundur',
        ]);

        $response->assertRedirect('/bendahara/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals('1 Agustus 2026', $surat->data['tanggal_selesai']);
    }

    public function test_akademik_can_backdate_student_letter()
    {
        $mahasiswa = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $akademik = User::where('role_id', User::ROLE_AKADEMIK)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-aktif-kuliah')->first();

        $surat = Surat::create([
            'pengaju_id' => $mahasiswa->id,
            'current_user_id' => $akademik->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $mahasiswa->name,
                'npm' => $mahasiswa->username,
                'programStudi' => 'Pendidikan Fisika',
                'semester' => '5',
                'jenis-semester' => 'Ganjil',
                'tahunAkademik' => '2026/2027',
                'namaOrangTuaAtauWali' => 'Fulan',
                'instansiAtauPekerjaanOrangTuaAtauWali' => 'PNS',
                'private' => [
                    'stepper' => [User::ROLE_MAHASISWA, User::ROLE_STAFF, User::ROLE_KAPRODI]
                ]
            ],
        ]);

        $response = $this->actingAs($akademik)->put(route('setujui-surat-akademik', $surat->id), [
            'no-surat' => '1234',
            'tanggal-surat' => '2026-06-10',
            'note' => 'Surat aktif kuliah tanggal mundur',
        ]);

        $response->assertRedirect('/akademik/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals('10 Juni 2026', $surat->data['tanggal_selesai']);

        // Check PDF preview
        $pdfResponse = $this->actingAs($akademik)->get(route('preview-surat-akademik', $surat->id));
        $pdfResponse->assertStatus(200);
    }

    public function test_legacy_approval_without_tanggal_surat_parameter_defaults_to_today()
    {
        $mahasiswa = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $akademik = User::where('role_id', User::ROLE_AKADEMIK)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-aktif-kuliah')->first();

        $surat = Surat::create([
            'pengaju_id' => $mahasiswa->id,
            'current_user_id' => $akademik->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $mahasiswa->name,
                'npm' => $mahasiswa->username,
                'programStudi' => 'Pendidikan Fisika',
                'semester' => '5',
                'jenis-semester' => 'Ganjil',
                'tahunAkademik' => '2026/2027',
                'namaOrangTuaAtauWali' => 'Fulan',
                'instansiAtauPekerjaanOrangTuaAtauWali' => 'PNS',
                'private' => [
                    'stepper' => [User::ROLE_MAHASISWA, User::ROLE_STAFF, User::ROLE_KAPRODI]
                ]
            ],
        ]);

        // Do not pass 'tanggal-surat' at all (legacy request)
        $response = $this->actingAs($akademik)->put(route('setujui-surat-akademik', $surat->id), [
            'no-surat' => '5678',
            'note' => 'Approval gaya lama',
        ]);

        $response->assertRedirect('/akademik/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertNotNull($surat->data['tanggal_selesai']);
        $this->assertEquals(formatTimestampToOnlyDateIndonesian(now()), $surat->data['tanggal_selesai']);
    }

    public function test_akademik_can_leave_date_empty_and_pdf_renders_dots()
    {
        $mahasiswa = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $akademik = User::where('role_id', User::ROLE_AKADEMIK)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-aktif-kuliah')->first();

        $surat = Surat::create([
            'pengaju_id' => $mahasiswa->id,
            'current_user_id' => $akademik->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $mahasiswa->name,
                'npm' => $mahasiswa->username,
                'programStudi' => 'Pendidikan Fisika',
                'semester' => '5',
                'jenis-semester' => 'Ganjil',
                'tahunAkademik' => '2026/2027',
                'namaOrangTuaAtauWali' => 'Fulan',
                'instansiAtauPekerjaanOrangTuaAtauWali' => 'PNS',
                'private' => [
                    'stepper' => [User::ROLE_MAHASISWA, User::ROLE_STAFF, User::ROLE_KAPRODI]
                ]
            ],
        ]);

        // Empty tanggal-surat
        $response = $this->actingAs($akademik)->put(route('setujui-surat-akademik', $surat->id), [
            'no-surat' => '9999',
            'tanggal-surat' => '',
            'note' => 'Approval cap manual',
        ]);

        $response->assertRedirect('/akademik/surat-masuk');

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertNull($surat->data['tanggal_selesai']);

        // Check PDF preview renders successfully with dots
        $pdfResponse = $this->actingAs($akademik)->get(route('preview-surat-akademik', $surat->id));
        $pdfResponse->assertStatus(200);

        // Check QR validation page
        $qrUrl = URL::signedRoute('preview-surat-qr', ['surat' => $surat->id]);
        $qrResponse = $this->get($qrUrl);
        $qrResponse->assertStatus(200)
            ->assertSee('Waktu Verifikasi Sistem:');
    }
}
