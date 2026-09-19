<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LegacySuratRegressionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * SMOKE TEST:
     * Memastikan form dari SELURUH jenis surat mahasiswa (13 jenis surat)
     * dapat dibuka dengan normal tanpa error 500 atau route missing.
     */
    public function test_all_mahasiswa_form_pages_load_successfully()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $daftarSuratMhs = JenisSurat::where('user_type', 'mahasiswa')->get();

        $this->assertNotEmpty($daftarSuratMhs);

        foreach ($daftarSuratMhs as $jenisSurat) {
            $response = $this->actingAs($mhs)->get('/mahasiswa/pengajuan-surat/' . $jenisSurat->slug);
            $response->assertStatus(200);
        }
    }

    /**
     * SMOKE TEST:
     * Memastikan form dari SELURUH jenis surat staf (7 jenis surat)
     * dapat dibuka dengan normal tanpa error 500.
     */
    public function test_all_staff_form_pages_load_successfully()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $daftarSuratStaff = JenisSurat::where('user_type', 'staff')->get();

        $this->assertNotEmpty($daftarSuratStaff);

        foreach ($daftarSuratStaff as $jenisSurat) {
            $response = $this->actingAs($staff)->get('/staff/pengajuan-surat/' . $jenisSurat->slug);
            $response->assertStatus(200);
        }
    }

    /**
     * REGRESSION TEST:
     * Menguji alur Surat Tugas (fitur lama) dengan penomoran opsional (dikosongkan).
     * Memastikan perubahan kode nullable noSurat tidak merusak alur Surat Tugas.
     */
    public function test_surat_tugas_approval_chain_with_optional_no_surat()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $dekan = User::where('role_id', User::ROLE_DEKAN)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-tugas')->first();

        // 1. Staf submit Surat Tugas
        $prodi = ProgramStudi::first();
        $response = $this->actingAs($staff)->post(route('staff-store-surat-tugas', $jenisSurat->slug), [
            'name' => $staff->name,
            'username' => $staff->username,
            'program-studi' => $prodi->id,
            'email' => 'dosen@fkip.unib.ac.id',
            'nama-dosen' => 'Dr. Ahmad Fauzi, M.Pd.',
            'nip-dosen' => '198001012005011001',
            'pangkat-dosen' => 'Penata Tk. I / III d',
            'jabatan-fungsional-dosen' => 'Lektor Kepala',
            'acara' => 'Menjadi Juri Lomba Karya Ilmiah Guru Tingkat Nasional',
            'tempat' => 'Hotel Santika Bengkulu',
            'waktu-mulai-penugasan' => '2026-10-01',
            'waktu-selesai-penugasan' => '2026-10-03',
            'dasar-penugasan' => 'Surat Undangan dari Kemendikdasmen No. 123/A/2026',
            'penerima' => $kaprodi->id,
        ]);

        $response->assertRedirect('/staff/riwayat-pengajuan-surat');

        $surat = Surat::where('pengaju_id', $staff->id)
            ->where('jenis_surat_id', $jenisSurat->id)
            ->latest()
            ->first();

        $this->assertNotNull($surat);
        $this->assertEquals($kaprodi->id, $surat->current_user_id);

        // 2. Kaprodi menyetujui -> diteruskan ke Dekan
        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $dekan->id,
            ]);

        $surat->refresh();
        $this->assertEquals($dekan->id, $surat->current_user_id);

        // 3. Dekan menyetujui -> diteruskan ke Staff Dekan
        $this->actingAs($dekan)
            ->put('/dekan/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $staffDekan->id,
            ]);

        $surat->refresh();
        $this->assertEquals($staffDekan->id, $surat->current_user_id);

        // 4. Staff Dekan menyetujui dengan NO SURAT DIKOSONGKAN
        $this->actingAs($staffDekan)
            ->put('/staff-dekan/surat-disetujui/' . $surat->id, [
                'no-surat' => '', // Dikosongkan sesuai permintaan WD1
                'note' => 'Disetujui tanpa no surat, nomor manual di TU'
            ]);

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEmpty($surat->data['noSurat']);

        // 5. Preview PDF Surat Tugas berhasil dirender tanpa error
        $pdfResponse = $this->actingAs($staff)->get('/staff/preview-surat/' . $surat->id);
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }

    /**
     * REGRESSION TEST:
     * Menguji alur Surat Tugas ketika nomor surat diisi.
     */
    public function test_surat_tugas_approval_chain_with_filled_no_surat()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();
        $jenisSurat = JenisSurat::where('slug', 'surat-tugas')->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $staffDekan->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'username' => $staff->username,
                'namaDosen' => 'Dr. Dosen FKIP',
                'nipDosen' => '198001012005011001',
                'pangkatDosen' => 'Penata / III c',
                'jabatanFungsionalDosen' => 'Lektor',
                'acara' => 'Seminar Pendidikan',
                'tempat' => 'Ruang Sidang FKIP',
                'dasarPenugasan' => 'Undangan Rektor',
                'private' => [
                    'stepper' => [3, 4, 8]
                ]
            ]
        ]);

        $this->actingAs($staffDekan)
            ->put('/staff-dekan/surat-disetujui/' . $surat->id, [
                'no-surat' => '1234',
                'note' => 'Disetujui dengan nomor surat'
            ]);

        $surat->refresh();
        $this->assertEquals('selesai', $surat->status);
        $this->assertEquals('1234', $surat->data['noSurat']);

        $pdfResponse = $this->actingAs($staff)->get('/staff/preview-surat/' . $surat->id);
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }

    /**
     * SMOKE TEST:
     * Memastikan seluruh halaman surat-masuk dan riwayat persetujuan
     * dapat dirender tanpa exception (misal: Undefined array key npm/email)
     * saat ada surat mahasiswa maupun staf di antrean.
     */
    public function test_all_inbox_and_history_pages_load_successfully_with_letters()
    {
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $wd3 = User::where('role_id', User::ROLE_WD3)->first();
        $wd2 = User::where('role_id', User::ROLE_WD2)->first();
        $kabag = User::where('role_id', User::ROLE_KABAG)->first();
        $bendahara = User::where('role_id', User::ROLE_BENDAHARA)->first();
        $dekan = User::where('role_id', User::ROLE_DEKAN)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $tu = User::where('role_id', User::ROLE_TATA_USAHA)->first();

        // Buat dummy surat di inbox kaprodi (sengaja tanpa 'npm' dan 'email' untuk memastikan view tahan banting)
        $jenisSurat = JenisSurat::first();
        $dummySurat = Surat::create([
            'pengaju_id' => $mhs->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $mhs->name,
                'username' => $mhs->username,
            ]
        ]);

        // Buat record Approval untuk masing-masing approver agar riwayat-persetujuan terisi data surat dummy
        $approvers = [$kaprodi, $wd3, $wd2, $kabag, $bendahara, $dekan, $staffDekan, $tu];
        foreach ($approvers as $approver) {
            if ($approver) {
                \App\Models\Approval::create([
                    'user_id' => $approver->id,
                    'surat_id' => $dummySurat->id,
                    'isApproved' => true,
                    'note' => 'setuju'
                ]);
            }
        }

        $this->actingAs($kaprodi)->get('/kaprodi/surat-masuk')->assertStatus(200);
        $this->actingAs($kaprodi)->get('/kaprodi/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($wd3)->get('/wd3/surat-masuk')->assertStatus(200);
        $this->actingAs($wd3)->get('/wd3/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($wd2)->get('/wd2/surat-masuk')->assertStatus(200);
        $this->actingAs($wd2)->get('/wd2/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($kabag)->get('/kabag/surat-masuk')->assertStatus(200);
        $this->actingAs($kabag)->get('/kabag/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($bendahara)->get('/bendahara/surat-masuk')->assertStatus(200);
        $this->actingAs($bendahara)->get('/bendahara/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($tu)->get('/tata-usaha/surat-masuk')->assertStatus(200);
        $this->actingAs($tu)->get('/tata-usaha/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($dekan)->get('/dekan/surat-masuk')->assertStatus(200);
        $this->actingAs($dekan)->get('/dekan/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($staffDekan)->get('/staff-dekan/surat-masuk')->assertStatus(200);
        $this->actingAs($staffDekan)->get('/staff-dekan/riwayat-persetujuan')->assertStatus(200);
        $this->actingAs($mhs)->get('/mahasiswa/riwayat-pengajuan-surat')->assertStatus(200);
        $this->actingAs($staff)->get('/staff/riwayat-pengajuan-surat')->assertStatus(200);
    }
}
