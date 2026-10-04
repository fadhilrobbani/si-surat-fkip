<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Surat;
use App\Models\Approval;
use App\Models\JenisSurat;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DekanWdShowSuratCompatibilityTest extends TestCase
{
    use DatabaseTransactions;

    private function makeKeluar($pengajuId, $currentUserId, array $extraData = [])
    {
        $jenisSurat = JenisSurat::where('slug', 'surat-keluar')->first();

        return Surat::create([
            'pengaju_id' => $pengajuId,
            'current_user_id' => $currentUserId,
            'jenis_surat_id' => $jenisSurat->id,
            'status' => 'diproses',
            'data' => array_merge([
                'perihal' => 'Surat Undangan Rapat',
                'paragrafAwal' => '<p>Paragraf pembuka HTML</p>',
                'paragrafAkhir' => '<p>Paragraf penutup HTML</p>',
            ], $extraData),
        ]);
    }

    public function test_dekan_show_surat_renders_html_raw_and_handles_arrays()
    {
        $dekan = User::where('role_id', User::ROLE_DEKAN)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $surat = $this->makeKeluar($staffDekan->id, $dekan->id, [
            'items' => [['uraian' => 'Konsumsi Rapat', 'nominal' => 150000]],
        ]);

        $response = $this->actingAs($dekan)->get(route('show-surat-dekan', $surat->id));

        $response->assertStatus(200);
        // HTML scalar tetap dirender mentah (backward compatible, tidak di-escape)
        $response->assertSee('<p>Paragraf pembuka HTML</p>', false);
        $response->assertDontSee('&lt;p&gt;Paragraf pembuka HTML&lt;/p&gt;');
        // Field array tidak menyebabkan error dan ikut terender
        $response->assertSee('Konsumsi Rapat');
    }

    public function test_wd_show_surat_renders_html_raw_and_handles_arrays()
    {
        $wd = User::where('role_id', User::ROLE_WD1)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $surat = $this->makeKeluar($staffDekan->id, $wd->id, [
            'items' => [['uraian' => 'Honor Narasumber', 'nominal' => 500000]],
        ]);

        $response = $this->actingAs($wd)->get(route('show-surat-wd', $surat->id));

        $response->assertStatus(200);
        $response->assertSee('<p>Paragraf pembuka HTML</p>', false);
        $response->assertDontSee('&lt;p&gt;Paragraf pembuka HTML&lt;/p&gt;');
        $response->assertSee('Honor Narasumber');
    }

    public function test_dekan_show_approval_renders_html_raw_and_handles_arrays()
    {
        $dekan = User::where('role_id', User::ROLE_DEKAN)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $surat = $this->makeKeluar($staffDekan->id, $dekan->id, [
            'items' => [['uraian' => 'Konsumsi Rapat', 'nominal' => 150000]],
        ]);

        $approval = Approval::create([
            'user_id' => $dekan->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => 'setuju',
        ]);

        $response = $this->actingAs($dekan)->get(route('show-approval-dekan', $approval->id));

        $response->assertStatus(200);
        $response->assertSee('<p>Paragraf pembuka HTML</p>', false);
        $response->assertDontSee('&lt;p&gt;Paragraf pembuka HTML&lt;/p&gt;');
        $response->assertSee('Konsumsi Rapat');
    }

    public function test_wd_show_approval_renders_html_raw_and_handles_arrays()
    {
        $wd = User::where('role_id', User::ROLE_WD1)->first();
        $staffDekan = User::where('role_id', User::ROLE_STAFF_DEKAN)->first();

        $surat = $this->makeKeluar($staffDekan->id, $wd->id, [
            'items' => [['uraian' => 'Honor Narasumber', 'nominal' => 500000]],
        ]);

        $approval = Approval::create([
            'user_id' => $wd->id,
            'surat_id' => $surat->id,
            'isApproved' => true,
            'note' => 'setuju',
        ]);

        $response = $this->actingAs($wd)->get(route('show-approval-wd', $approval->id));

        $response->assertStatus(200);
        $response->assertSee('<p>Paragraf pembuka HTML</p>', false);
        $response->assertDontSee('&lt;p&gt;Paragraf pembuka HTML&lt;/p&gt;');
        $response->assertSee('Honor Narasumber');
    }
}
