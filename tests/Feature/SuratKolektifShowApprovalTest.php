<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use App\Models\JenisSurat;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratKolektifShowApprovalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_show_approval_pages_render_batch_mahasiswa_without_error()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        foreach (['surat-cuti-mahasiswa', 'surat-izin-observasi', 'surat-penundaan-pembayaran-ukt'] as $slug) {
            $jenisSurat = JenisSurat::where('slug', $slug)->first();

            $surat = Surat::create([
                'pengaju_id' => $staff->id,
                'current_user_id' => $staff->id,
                'jenis_surat_id' => $jenisSurat->id,
                'status' => 'diproses',
                'data' => [
                    'nama' => $staff->name,
                    'mahasiswa' => [[
                        'nama' => 'Delia Gustina',
                        'npm' => 'A1D021019',
                        'programStudi' => 'S1 Pendidikan Biologi',
                        'alasanCuti' => 'Bekerja',
                        'hariTanggalUjian' => 'Senin, 6 Juli 2026',
                        'waktu' => '08.00-10.00',
                    ]],
                    'private' => ['stepper' => [User::ROLE_STAFF, User::ROLE_KAPRODI, User::ROLE_STAFF_DEKAN]],
                ],
            ]);

            $kaprodiApproval = Approval::create([
                'user_id' => $kaprodi->id,
                'surat_id' => $surat->id,
                'isApproved' => true,
                'note' => 'setuju',
            ]);

            $staffDekanApproval = Approval::create([
                'user_id' => $staffDekan->id,
                'surat_id' => $surat->id,
                'isApproved' => true,
                'note' => 'diselesaikan',
            ]);

            $this->actingAs($kaprodi)
                ->get(route('show-approval-kaprodi', $kaprodiApproval->id))
                ->assertStatus(200)
                ->assertSee('Delia Gustina');

            $this->actingAs($staffDekan)
                ->get(route('show-approval-staff-dekan', $staffDekanApproval->id))
                ->assertStatus(200)
                ->assertSee('Delia Gustina');
        }
    }
}
