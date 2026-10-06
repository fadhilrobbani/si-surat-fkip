<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KaprodiSignerFieldsTest extends TestCase
{
    use DatabaseTransactions;

    private function makeSuratForKaprodi(string $slug)
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $jenisSurat = JenisSurat::where('slug', $slug)->first();

        $surat = Surat::create([
            'pengaju_id' => $staff->id,
            'current_user_id' => $kaprodi->id,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => [
                'nama' => $staff->name,
                'private' => ['stepper' => [User::ROLE_STAFF]],
            ],
        ]);

        return [$surat, $kaprodi];
    }

    public function test_capo_signature_field_is_shown_on_kaprodi_approval_form()
    {
        [$surat, $kaprodi] = $this->makeSuratForKaprodi('surat-permohonan-narasumber');

        $response = $this->actingAs($kaprodi)->get(route('show-surat-kaprodi', $surat->id));

        $response->assertStatus(200);
        $response->assertSee('Penandatangan Surat');
        $response->assertSee('name="nama_kaprodi"', false);
        $response->assertSee('name="nip_kaprodi"', false);
        $response->assertSee('name="simpan_ke_profil"', false);
    }

    public function test_hard_block_when_signer_name_is_placeholder()
    {
        [$surat, $kaprodi] = $this->makeSuratForKaprodi('surat-permohonan-narasumber');
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $response = $this->actingAs($kaprodi)
            ->from(route('show-surat-kaprodi', $surat->id))
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $staffDekan->id,
                'nama_kaprodi' => 'Kaprodi S1 Pendidikan Biologi',
                'nip_kaprodi' => '198001012000011001',
            ]);

        $response->assertSessionHasErrors('nama_kaprodi');

        $surat->refresh();
        $this->assertEquals($kaprodi->id, $surat->current_user_id, 'Surat tidak boleh diteruskan saat nama placeholder.');
    }

    public function test_hard_block_when_signer_nip_is_empty()
    {
        [$surat, $kaprodi] = $this->makeSuratForKaprodi('surat-permohonan-narasumber');
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $response = $this->actingAs($kaprodi)
            ->from(route('show-surat-kaprodi', $surat->id))
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $staffDekan->id,
                'nama_kaprodi' => 'Dr. Budi Santoso, M.Pd.',
                'nip_kaprodi' => '',
            ]);

        $response->assertSessionHasErrors('nip_kaprodi');
    }

    public function test_kaprodi_can_submit_corrected_signer_and_it_is_persisted()
    {
        [$surat, $kaprodi] = $this->makeSuratForKaprodi('surat-permohonan-narasumber');
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $staffDekan->id,
                'nama_kaprodi' => 'Dr. Budi Santoso, M.Pd.',
                'nip_kaprodi' => '198001012000011001',
            ])
            ->assertRedirect('/kaprodi/surat-masuk');

        $surat->refresh();
        $this->assertEquals('Dr. Budi Santoso, M.Pd.', $surat->data['private']['namaKaprodi']);
        $this->assertEquals('198001012000011001', $surat->data['private']['nipKaprodi']);
    }

    public function test_save_to_profile_checkbox_updates_user_account()
    {
        [$surat, $kaprodi] = $this->makeSuratForKaprodi('surat-permohonan-narasumber');
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $this->actingAs($kaprodi)
            ->put('/kaprodi/surat-staff-disetujui/' . $surat->id, [
                'penerima' => $staffDekan->id,
                'nama_kaprodi' => 'Dr. Budi Santoso, M.Pd.',
                'nip_kaprodi' => '198001012000011001',
                'simpan_ke_profil' => '1',
            ])
            ->assertRedirect('/kaprodi/surat-masuk');

        $kaprodi->refresh();
        $this->assertEquals('Dr. Budi Santoso, M.Pd.', $kaprodi->name);
        $this->assertEquals('198001012000011001', $kaprodi->nip);
    }

    public function test_non_signer_letters_do_not_require_signer_fields()
    {
        // surat-tugas tidak ditandatangani Kaprodi (tidak ditampilkan field-nya).
        [$surat, $kaprodi] = $this->makeSuratForKaprodi('surat-tugas');

        $response = $this->actingAs($kaprodi)->get(route('show-surat-kaprodi', $surat->id));
        $response->assertStatus(200);
        $response->assertDontSee('name="nama_kaprodi"', false);
    }
}
