<?php

namespace Tests\Feature;

use App\Models\EmailLog;
use App\Models\User;
use App\Services\EmailNotifikasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Tests\TestCase;

class StatistikEmailTest extends TestCase
{
    use DatabaseTransactions;

    private function user(): User
    {
        return User::factory()->create(['role_id' => 3, 'username' => 'stat-uji', 'email' => 'stat-uji@example.test', 'password' => bcrypt('x')]);
    }

    public function test_email_terkirim_tercatat_dengan_kategori(): void
    {
        $this->user();
        config(['mail.default' => 'array']);

        Password::sendResetLink(['email' => 'stat-uji@example.test', 'password' => bcrypt('x')]);

        $this->assertDatabaseHas('email_logs', ['kategori' => 'reset_password', 'status' => 'terkirim', 'penerima' => 'stat-uji@example.test']);
    }

    public function test_email_ditolak_smtp_tercatat_gagal(): void
    {
        $this->user();
        Mail::extend('gagal', fn () => new class extends AbstractTransport {
            protected function doSend(SentMessage $message): void
            {
                throw new TransportException('550 daily quota exceeded');
            }

            public function __toString(): string
            {
                return 'gagal://';
            }
        });
        config(['mail.mailers.gagal' => ['transport' => 'gagal'], 'mail.default' => 'gagal']);

        try {
            Password::sendResetLink(['email' => 'stat-uji@example.test', 'password' => bcrypt('x')]);
        } catch (TransportException) {
        }

        $this->assertDatabaseHas('email_logs', ['kategori' => 'reset_password', 'status' => 'gagal']);
    }

    public function test_notifikasi_mati_tercatat_dilewati(): void
    {
        EmailNotifikasi::kirim('pemohon@example.test', new Mailable());

        $this->assertDatabaseHas('email_logs', ['kategori' => 'notifikasi_surat', 'status' => 'dilewati', 'penerima' => 'pemohon@example.test']);
    }

    public function test_notifikasi_surat_terkirim_berkategori_notifikasi(): void
    {
        config(['mail.notifikasi_surat' => true, 'mail.default' => 'array']);

        EmailNotifikasi::kirim('pemohon@example.test', (new Mailable())->subject('Uji')->html('<p>x</p>'));

        $this->assertDatabaseHas('email_logs', ['kategori' => 'notifikasi_surat', 'status' => 'terkirim']);
    }

    public function test_admin_bisa_buka_halaman_statistik(): void
    {
        config(['mail.kuota_harian' => 100]);
        EmailLog::create(['kategori' => 'reset_password', 'status' => 'terkirim']);
        $admin = User::factory()->create(['role_id' => 1, 'username' => 'admin-stat', 'password' => bcrypt('x')]);

        $this->actingAs($admin)->get('/admin/statistik-email')->assertOk()->assertSee('Statistik Email');
    }

    public function test_non_admin_tidak_bisa_buka_halaman_statistik(): void
    {
        $this->actingAs($this->user())->get('/admin/statistik-email')->assertForbidden();
    }

    public function test_semua_widget_statistik_bisa_dirender_dengan_data(): void
    {
        config(['mail.kuota_harian' => 100]);
        $this->actingAs(User::factory()->create(['role_id' => 1, 'username' => 'admin-w', 'password' => bcrypt('x')]));
        foreach (['terkirim', 'gagal', 'dilewati'] as $status) {
            EmailLog::create(['kategori' => 'notifikasi_surat', 'status' => $status, 'role_id' => 3, 'route' => 'setujui-surat-kabag']);
        }

        foreach ([
            \App\Filament\Widgets\Email\EmailStatsWidget::class,
            \App\Filament\Widgets\Email\EmailHarianChart::class,
            \App\Filament\Widgets\Email\EmailKategoriChart::class,
            \App\Filament\Widgets\Email\EmailAsalChart::class,
            \App\Filament\Widgets\Email\EmailPerJamChart::class,
        ] as $widget) {
            \Livewire\Livewire::test($widget)->assertOk();
        }

        \Livewire\Livewire::test(\App\Filament\Widgets\Email\EmailAsalChart::class)->set('filter', 'route')->assertOk();
        \Livewire\Livewire::test(\App\Filament\Widgets\Email\EmailHarianChart::class)->set('filter', '90')->assertOk();
        \Livewire\Livewire::test(\App\Filament\Pages\StatistikEmail::class)->assertOk()->assertSee('Log email');
    }
}
