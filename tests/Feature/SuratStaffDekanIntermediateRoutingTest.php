<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\JenisSurat;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SuratStaffDekanIntermediateRoutingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_kaprodi_show_surat_offers_staff_dekan_for_all_three_new_letter_types()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $kaprodi = User::where('role_id', User::ROLE_KAPRODI)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $slugs = [
            'surat-permohonan-narasumber',
            'surat-peminjaman-ruang',
            'surat-pencairan-dana',
        ];

        foreach ($slugs as $slug) {
            $jenisSurat = JenisSurat::where('slug', $slug)->first();
            $surat = Surat::create([
                'pengaju_id' => $staff->id,
                'current_user_id' => $kaprodi->id,
                'jenis_surat_id' => $jenisSurat->id,
                'status' => 'diproses',
                'data' => [
                    'nama' => $staff->name,
                    'private' => ['stepper' => [User::ROLE_STAFF]]
                ]
            ]);

            $view = $this->actingAs($kaprodi)->get(route('show-surat-kaprodi', $surat->id));
            $view->assertStatus(200);

            $penerimaList = collect($view->viewData('daftarPenerima'));
            $this->assertTrue($penerimaList->pluck('id')->contains($staffDekan->id), "Kaprodi recipient list must contain Staff Dekan for {$slug}");
            $this->assertEquals(User::ROLE_STAFF_DEKAN, $penerimaList->first()->role_id, "Kaprodi recipient default must be Staff Dekan for {$slug}");
        }
    }

    public function test_staff_dekan_offers_correct_default_leader_for_each_letter()
    {
        $staff = User::where('role_id', User::ROLE_STAFF)->first();
        $mhs = User::where('role_id', User::ROLE_MAHASISWA)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();
        $dekan = User::where('role_id', User::ROLE_DEKAN)->first();
        $wd2 = User::where('role_id', User::ROLE_WD2)->first();
        $wd3 = User::where('role_id', User::ROLE_WD3)->first();

        $matrix = [
            ['slug' => 'surat-permohonan-narasumber', 'user' => $staff, 'expected_leader' => $dekan->id, 'expected_role' => User::ROLE_DEKAN],
            ['slug' => 'surat-peminjaman-ruang', 'user' => $staff, 'expected_leader' => $wd2->id, 'expected_role' => User::ROLE_WD2],
            ['slug' => 'surat-pencairan-dana', 'user' => $staff, 'expected_leader' => $wd2->id, 'expected_role' => User::ROLE_WD2],
            ['slug' => 'surat-peminjaman-ruang-mahasiswa', 'user' => $mhs, 'expected_leader' => $wd3->id, 'expected_role' => User::ROLE_WD3],
            ['slug' => 'surat-pencairan-dana-mahasiswa', 'user' => $mhs, 'expected_leader' => $wd3->id, 'expected_role' => User::ROLE_WD3],
        ];

        foreach ($matrix as $row) {
            $jenisSurat = JenisSurat::where('slug', $row['slug'])->first();
            $surat = Surat::create([
                'pengaju_id' => $row['user']->id,
                'current_user_id' => $staffDekan->id,
                'jenis_surat_id' => $jenisSurat->id,
                'status' => 'diproses',
                'data' => [
                    'nama' => $row['user']->name,
                    'private' => ['stepper' => [$row['user']->role_id, User::ROLE_KAPRODI]]
                ]
            ]);

            $view = $this->actingAs($staffDekan)->get(route('show-surat-staff-dekan', $surat->id));
            $view->assertStatus(200);

            $penerimaList = collect($view->viewData('daftarPenerima'));
            $this->assertNotEmpty($penerimaList);
            $this->assertEquals($row['expected_leader'], $penerimaList->first()->id, "Default leader for {$row['slug']} must match expected recipient");
            $this->assertEquals($row['expected_role'], $penerimaList->first()->role_id, "Default leader role for {$row['slug']} must match");

            // Test forwarding without explicit recipient falls back to correct default leader
            $response = $this->actingAs($staffDekan)->put(route('setujui-surat-staff-staff-dekan', $surat->id), [
                'note' => "Forwarded {$row['slug']} to leadership"
            ]);
            $response->assertRedirect('/staff-dekan/surat-masuk');

            $surat->refresh();
            $this->assertEquals($row['expected_leader'], $surat->current_user_id, "Letter {$row['slug']} must be assigned to expected leader");
            $this->assertEquals('diproses', $surat->status);
            $this->assertContains(User::ROLE_STAFF_DEKAN, $surat->data['private']['stepper']);
        }
    }
}
